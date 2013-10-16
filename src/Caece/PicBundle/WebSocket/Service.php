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
    
    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onClose(ConnectionInterface $conn)
    {
        
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        
    }

    public function onOpen(ConnectionInterface $conn)
    {
        
    }
}
