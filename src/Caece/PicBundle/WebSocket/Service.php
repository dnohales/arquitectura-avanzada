<?php

namespace Caece\PicBundle\WebSocket;

use Caece\PicBundle\Entity\ChannelReading;
use Caece\PicBundle\Settings\SettingsManager;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use JMS\DiExtraBundle\Annotation as DI;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

/**
 * Description of Server
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 * 
 * @DI\Service("caecepic_websocket_service")
 */
class Service implements MessageComponentInterface
{
    private $em;
    
    private $settingsManager;
    
    private $connections;
    
    private $lastTimestamp;
    
    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "settingsManager" = @DI\Inject("caecepic_settings_manager")
     * })
     */
    public function __construct(EntityManager $em, SettingsManager $settingsManager)
    {
        $this->em = $em;
        $this->settingsManager = $settingsManager;
        $this->connections = new SplObjectStorage();
        $this->lastTimestamp = new DateTime();
        
        $this->settingsManager->loadSettings();
    }
    
    private function serializeReading(ChannelReading $r)
    {
        return array(
            'channel' => $r->getChannel(),
            'rawData' => $r->getRawData(),
            'convertedData' => $this->settingsManager->getSettings()->convertReading($r),
            'readedAt' => $r->getReadedAt()->format(\DateTime::ISO8601),
        );
    }
    
    private function serializeReadingList(array $readings)
    {
        $return = array();
        
        foreach ($readings as $r) {
            $return[] = $this->serializeReading($r);
        }
        
        return $return;
    }
    
    public function check()
    {
        $readings = $this->em->getRepository('CaecePicBundle:ChannelReading')->findFromDate($this->lastTimestamp);
        
        if (count($readings) > 0) {
            $this->debug('Se encontraron '.count($readings).' lecturas nuevas');
            
            $response = array(
                'readings' => $this->serializeReadingList($readings),
                'reloadConfig' => false
            );
            
            if ($this->settingsManager->wasChanged()) {
                $response['reloadConfig'] = true;
                $this->settingsManager->loadSettings();
            }
            
            $this->lastTimestamp = end($readings)->getReadedAt();
            
            $this->sendToAll(json_encode($response));
        }
        
        $this->em->clear();
    }
    
    public function sendToAll($msg)
    {
        $this->debug('Sending: '.$msg);
        foreach ($this->connections as $conn) {
            $conn->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->debug('[Close-'.$conn->resourceId.'] '.$conn->remoteAddress.' cerró la conexión');
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->debug('[Error-'.$conn->resourceId.'] Ocurrió un error en la conexión con '.$conn->remoteAddress.'. '.$e->getMessage());
        $this->connections->detach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->debug('[Open-'.$conn->resourceId.'] '.$conn->remoteAddress.' estableció conexión.');
        $this->connections->attach($conn);
        
        //Se envían las últimas lecturas de cada canal al cliente recién conectado
        //para que pueda inicializar el gráfico o panel de administración
        $readings = $this->em->getRepository('CaecePicBundle:ChannelReading')->findLatestByChannel();
        $conn->send(json_encode(array(
            'readings' => $this->serializeReadingList($readings),
            'reloadConfig' => false
        )));
    }
    
    public function debug($msg)
    {
        echo '['.date('Y-m-d H:i:s').'] '.$msg."\n";
    }
}
