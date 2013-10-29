<?php
namespace Caece\PicBundle\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of PicMonitor
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class WebsocketServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:websocket:server')
            ->setDescription('Inicia el servidor websocket para proveer actualizaciones en tiempo real a los clientes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('caecepic_websocket_service');
        
        $port = $this->getContainer()->getParameter('caece_pic.websocket_server_port');
        
        $loop = Factory::create();
        $webSock = new Server($loop);
        $webSock->listen($port, '0.0.0.0');
        new IoServer(new HttpServer(new WsServer($service)), $webSock);
        $loop->addPeriodicTimer(1, function() use($service) {
            $service->check();
        });
        
        $output->writeln('Se ha iniciado el servicio WebSocket en el puerto '.$port);
        
        $loop->run();
    }
}
