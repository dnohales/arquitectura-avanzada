<?php
namespace Caece\PicBundle\PicDriver;

/**
 * Description of PIC18F2550Driver
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class Pic18F2550Driver implements DriverInterface
{
    public function __construct()
    {
        throw new \Exception('El driver real del PIC no está implementado');
    }

    public function connect($connectinData)
    {

    }

    public function read()
    {

    }

    public function send($command)
    {

    }
}
