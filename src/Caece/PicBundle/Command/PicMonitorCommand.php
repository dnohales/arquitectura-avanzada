<?php
namespace Caece\PicBundle\Command;

use Caece\PicBundle\Entity\ChannelReading;
use Caece\PicBundle\PicDriver\DummyDriver;
use Caece\PicBundle\PicDriver\PhpSerialDriver;
use Caece\PicBundle\PicDriver\Pic18F2550Driver;
use Caece\PicBundle\PicSensor\Sensor\SensorInterface;
use Caece\PicBundle\Settings\Settings;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of PicMonitor
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class PicMonitorCommand extends ContainerAwareCommand
{
    const MAIL_WAIT_INTERVAL = 300;
    
    private $mailTimeByChannel = array();
    
    private $solarCellOpenedSince = null;
    
    protected function configure()
    {
        $this
            ->setName('app:pic:monitor')
            ->setDescription('Arranca el monitor de PIC para guardar nuevas lecturas en la base de datos')
            ->addArgument('port', InputArgument::REQUIRED, 'El puerto donde se encuentra conectado el PIC, por ejemplo: COM3')
            ->addOption('dummy', null, InputOption::VALUE_NONE, 'Si esta opción está presente, se inicia el monitor dummy que genera datos aleatorios de prueba');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Comenzando monitoreo del PIC...</comment>');

        if ($input->getOption('dummy')) {
            $output->writeln('<info>ATENCIÓN: se está utilizando el driver dummy</info>');
            $driver = new DummyDriver();
        } else {
            $driver = new PhpSerialDriver();
        }
        
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        /* @var $settingsManager \Caece\PicBundle\Settings\SettingsManager */
        $settingsManager = $this->getContainer()->get('caecepic_settings_manager');
        $settingsManager->loadSettings();
        
        $driver->connect($this->getConnectionData($input));
        
        for (;;) {
            $readings = $driver->read();
            
            foreach ($readings as $r) {
                $this->processReadingData($r);
            }
            
            $em->flush();
            $em->clear();
            
            if ($settingsManager->wasChanged()) {
                $this->onReloadSettings($settingsManager->getSettings(), $settingsManager->loadSettings());
                sleep(1);
            }
            
            usleep(250000);
        }
    }
    
    private function processReadingData($r)
    {
        /* @var $settings Settings */
        $settings = $this->getContainer()->get('caecepic_settings_manager')->getSettings();
        
        //Se crea la expresión regular de la cadena enviada por el PIC, dependiendo
        //de los canales activados en el SCAN
        $readingRegEx = '/';
        $continuousChannelCount = $settings->getChannels()->getActiveContinuous()->count();
        for ($i = 0; $i < $continuousChannelCount; $i++) {
            $readingRegEx .= '\d\d\d\d;';
        }
        $readingRegEx .= '[0-3];/';
        
        //Verifico que cumpla con el formato <numero>;<numero>;<numero>;
        if (preg_match($readingRegEx, $r) >= 1) {
            $channelValues = explode(';', trim($r, ';'));
            
            //Se crean las lecturas para canales analógicos
            foreach (array_values($settings->getChannels()->getActiveContinuous()->toArray()) as $key => $channel) {
                $this->createReading($channel->getNumber(), $channelValues[$key]);
            }
            
            //Se crean las lecturas para canales digitales
            switch (intval(end($channelValues)))
            {
            case 0:
                $this->createReading(4, 0);
                $this->createReading(5, 0);
                break;
            case 1:
                $this->createReading(4, 1);
                $this->createReading(5, 0);
                break;
            case 2:
                $this->createReading(4, 0);
                $this->createReading(5, 1);
                break;
            case 3:
                $this->createReading(4, 1);
                $this->createReading(5, 1);
                break;
            }
        }
    }
    
    private function getConnectionData(InputInterface $input)
    {
        return array(
            'port' => $input->getArgument('port'),
            'binDirectory' => $this->getContainer()->get('kernel')->locateResource('@CaecePicBundle/Resources/bin')
        );
    }
    
    private function onReloadSettings(Settings $oldSettings, Settings $newSettings)
    {
        
    }
    
    private function createReading($channel, $value)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $reading = new ChannelReading();
                
        $reading->setChannel(intval($channel));
        $reading->setRawData(intval($value));

        $em->persist($reading);
        
        $this->onNewReading($reading);
    }
    
    private function sendMail($subject, $view, array $viewParameters = array())
    {
        /* @var $settings Settings */
        $settings = $this->getContainer()->get('caecepic_settings_manager')->getSettings();
        
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('pic@ucaecemdp.edu.ar')
            ->setTo($settings->getNotifyEmailAddress())
            ->setBody(
                $this->getContainer()->get('templating')->render($view, $viewParameters)
            )
        ;
        
        echo "[Mail] $subject\n";
        $this->getContainer()->get('mailer')->send($message);
    }
    
    private function canSendMailForChannel($channel)
    {
        $now = new \DateTime();
        return !isset($this->mailTimeByChannel[$channel]) ||
               $now->getTimestamp() - $this->mailTimeByChannel[$channel]->getTimestamp() > self::MAIL_WAIT_INTERVAL;
    }
    
    private function onNewReading(ChannelReading $reading)
    {
        /* @var $settings Settings */
        $settings = $this->getContainer()->get('caecepic_settings_manager')->getSettings();
        
        /* @var $channel \Caece\PicBundle\Settings\ChannelSetting */
        $channel = $settings->getChannels()->get($reading->getChannel());
        
        $now = new \DateTime();
        
        //Verifico umbrales en canales contínuos
        if ($channel->getSensor()->getType() == SensorInterface::TYPE_CONTINUOUS) {
            if ($channel->isOutOfThreshold($reading->getRawData())) {
                if ($this->canSendMailForChannel($reading->getChannel())) {
                    $this->sendMail('Se ha sobrepasado el umbral del canal '.$reading->getChannel(), 'CaecePicBundle:Mail:out-threshold.txt.twig', array(
                        'channel' => $channel,
                        'reading' => $reading,
                        'converted_value' => $settings->convertReading($reading)
                    ));
                    $this->mailTimeByChannel[$reading->getChannel()] = $now;
                }
            }
        }
        
        //Verifico la apertura del gabinete
        if ($reading->getChannel() == $settings->getChannels()->getBySensor('Caece\PicBundle\PicSensor\Sensor\Concrete\CabinetStatus')->first()->getNumber()) {
            if ($reading->getRawData() == 1) {
                if ($this->canSendMailForChannel($reading->getChannel())) {
                    $this->sendMail('Se ha abierto el gabinete', 'CaecePicBundle:Mail:open-cabinet.txt.twig');
                    $this->mailTimeByChannel[$reading->getChannel()] = $now;
                }
            }
        }
        
        //Verifico el estado del panel solar
        if ($reading->getChannel() == $settings->getChannels()->getBySensor('Caece\PicBundle\PicSensor\Sensor\Concrete\SolarCellStatus')->first()->getNumber()) {
            $nowTime = intval($now->format('Hi'));
            if ($reading->getRawData() == 1 && $nowTime > 600 && $nowTime < 1900) {
                if ($this->canSendMailForChannel($reading->getChannel())) {
                    $this->sendMail('La celda solar no está funcionando', 'CaecePicBundle:Mail:solar-cell.txt.twig');
                    $this->mailTimeByChannel[$reading->getChannel()] = $now;
                }
            }
        }
    }

}
