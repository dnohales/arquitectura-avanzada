<?php
namespace Caece\PicBundle\Command;

use Caece\PicBundle\Entity\ChannelReading;
use Caece\PicBundle\PicDriver\DummyDriver;
use Caece\PicBundle\PicDriver\Pic18F2550Driver;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
    protected function configure()
    {
        $this
            ->setName('app:pic:monitor')
            ->setDescription('Arranca el monitor de PIC para guardar nuevas lecturas en la base de datos')
            ->addOption('dummy', null, InputOption::VALUE_NONE, 'Si esta opción está presente, se inicia el monitor dummy que genera datos aleatorios de prueba');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Comenzando monitoreo del PIC...</comment>');

        if ($input->getOption('dummy')) {
            $output->writeln('<info>ATENCIÓN: se está utilizando el driver dummy</info>');
            $driver = new DummyDriver();
        } else {
            $driver = new Pic18F2550Driver();
        }
        
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        for (;;) {
            $readingList = $this->parseDriverData($driver->read());
            
            foreach ($readingList as $r) {
                $output->writeln("Channel=".$r->getChannel().', rawData='.$r->getRawData());
                $em->persist($r);
            }
            
            $em->flush();
            sleep(1);
        }
    }

    private function parseDriverData($data)
    {
        $readingList = array();

        foreach ($data as $channelNumber => $rawData) {
            $reading = new ChannelReading();
            $reading->setChannel($channelNumber);
            $reading->setRawData($rawData);

            $readingList[] = $reading;
        }

        return $readingList;
    }

}
