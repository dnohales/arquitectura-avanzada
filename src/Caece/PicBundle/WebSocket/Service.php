<?php

namespace Caece\PicBundle\WebSocket;

use Doctrine\ORM\EntityManager;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use JMS\DiExtraBundle\Annotation as DI;

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
    
    private $connections;
    
    private $lastTimestamp;
    
    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->connections = new \SplObjectStorage();
        $this->lastTimestamp = new \DateTime();
    }
    
    public function check()
    {
        
    }
    
    public function sendToAll($msg)
    {
        foreach ($this->connections as $conn) {
            $conn->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        echo '[Close-'.$conn->resourceId.'] '.$conn->remoteAddress.' cerró la conexión'."\n";
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo '[Error-'.$conn->resourceId.'] Ocurrió un error en la conexión con '.$conn->remoteAddress.'. '.$e->getMessage()."\n";
        $this->connections->detach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo '[Open-'.$conn->resourceId.'] '.$conn->remoteAddress.' estableció conexión.'."\n";
        $this->connections->attach($conn);
    }
}
