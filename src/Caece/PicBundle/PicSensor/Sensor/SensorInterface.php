<?php
namespace Caece\PicBundle\PicSensor\Sensor;

/**
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
interface SensorInterface
{
    /**
     * @return string El nombre del sensor
     */
    function getName();
    
    /**
     * @return string La descripción del valor que devuelve el sensor, por
     * ejemplo, "Temperatura"
     */
    function getReadedDataDescription();
    
    /**
     * Convierte el valor crudo obtenido por el sensor (usualmente un
     * valor de 0 a 1024) en el dato real legible por humanos. Por ejemplo,
     * para un sensor de temperatura, si el valor es 930, podría devolver 80, de
     * 80 ºC.
     * 
     * @param int $rawData El dato crudo.
     * @return string El dato real.
     */
    function convertRawData($rawData);
    
    /**
     * @return string El nombre de la unidad de medición que maneja el sensor,
     * por ejemplo, "ºC"
     */
    function getUnitName();
}

?>
