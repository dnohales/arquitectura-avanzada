<?php
namespace Caece\PicBundle\PicSensor\Sensor\Concrete;

use Caece\PicBundle\PicSensor\Sensor\SensorInterface;

class SolarRadiationSensor implements SensorInterface
{
    /**
     * @return string El nombre del sensor
     */
    function getName()
    {
        return "Sensor de radiación solar LDR";
    }
    
    /**
     * @return string La descripción del valor que devuelve el sensor, por
     * ejemplo, "Temperatura"
     */
    function getReadedDataDescription()
    {
        return "Radiación solar";
    }
    
    /**
     * Convierte el valor crudo obtenido por el sensor (usualmente un
     * valor de 0 a 1024) en el dato real legible por humanos. Por ejemplo,
     * para un sensor de temperatura, si el valor es 930, podría devolver 80, de
     * 80 ºC.
     * 
     * @param int $rawData El dato crudo.
     * @return string El dato real.
     */
    function convertRawData($rawData)
    {
        //se trabaja con el SDL y un resistor de 3.3K
        $aux = (2500/$rawData - 500)/3.3;
        if ($aux < 10)
        {
            $aux = 10;
        }
        else if ($aux > 100)
        {
            $aux = 100;
        }
        
        return $aux;
    }
    
    /**
     * @return string El nombre de la unidad de medición que maneja el sensor,
     * por ejemplo, "ºC"
     */
    function getUnitName()
    {
        return "w/m^2";
    }

    public function getType()
    {
        return self::TYPE_CONTINUOUS;
    }
}

?>
