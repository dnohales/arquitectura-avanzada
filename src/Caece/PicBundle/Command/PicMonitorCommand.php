<?php
namespace Caece\PicBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
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
            ->setDescription('Arranca el monitor de PIC para guardar nuevas lecturas en la base de datos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Comenzando monitoreo del PIC...');
        //TODO: leer el PIC y guardar huevadas en la base
    }
}
