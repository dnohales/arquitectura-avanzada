<?php
namespace Caece\PicBundle\Command;

use Ratchet\Server\IoServer;
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
        $server = IoServer::factory($service, 8080);
        $server->loop->addPeriodicTimer(1, function() {
            echo "Tick! En este momento debería buscarse en la DB por actualizaciones y mandárselas a los clientes.\n";
        });
        
        $output->writeln('Se ha iniciado el servicio WebSocket');
        
        $server->run();
    }
}
