<?php
namespace Caece\PicBundle\PicDriver;

/**
 * Description of DummyDriver
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class DummyDriver implements DriverInterface
{
    private $latestReadsByChannel;

    public function __construct()
    {
        //Se inicializan los datos
        $this->latestReadsByChannel = array(
            rand(10, 1024),
            rand(10, 1024),
            rand(10, 1024)
        );
    }

    
    public function connect($connectinData)
    {
    }

    public function send($command)
    {
    }

    /**
     * @param int $referenceData dato de referencia
     * @return int un valor de 0 a 1024 que esté mas o menos cerca de
     * $referenceData
     */
    private function randNearData($referenceData)
    {
        $multiplier = rand(0, 1) == 0? -1:1;
        $data = $referenceData + rand(0, 30) * $multiplier;

        if ($data > 1023) {
            return 1023;
        } else if ($data < 0) {
            return 0;
        } else {
            return $data;
        }
    }

    public function read()
    {
        foreach ($this->latestReadsByChannel as $key => $value) {
            $this->latestReadsByChannel[$key] = $this->randNearData($value);
        }

        return $this->latestReadsByChannel;
    }
}
