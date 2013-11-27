<?php
namespace Caece\PicBundle\PicSensor\Sensor\Concrete;

use Caece\PicBundle\PicSensor\Sensor\SensorInterface;

class TemperatureSensor implements SensorInterface
{
    /**
     * @return string El nombre del sensor
     */
    function getName()
    {
        return "Sensor de temperatura LM35";
    }
    
    /**
     * @return string La descripción del valor que devuelve el sensor, por
     * ejemplo, "Temperatura"
     */
    function getReadedDataDescription()
    {
        return "Temperatura ambiental";
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
        //rawData tiene el voltaje en miliVolts?
        $aux = $rawData / 10;
        if ($aux < -30)
        {
            $aux = -30;
        }
        else if ($aux > 120)
        {
            $aux = 120;
        }
        
        return $aux;
    }
    
    /**
     * @return string El nombre de la unidad de medición que maneja el sensor,
     * por ejemplo, "ºC"
     */
    function getUnitName()
    {
        return "ºC";
    }
    
    public function getType()
    {
        return self::TYPE_CONTINUOUS;
    }
}

?>
