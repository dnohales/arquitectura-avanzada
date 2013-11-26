<?php
namespace Caece\PicBundle\PicSensor\Sensor;

/**
 * Description of AbstractBooleanSensor
 *
 * @author Damian
 */
abstract class AbstractBooleanSensor implements SensorInterface
{
    public function getReadedDataDescription()
    {
        return 'N/A';
    }

    public function getUnitName()
    {
        return '';
    }
    
    public function convertRawData($rawData)
    {
        return $rawData == 1? true:false;
    }

    public function getType()
    {
        return self::TYPE_BOOLEAN;
    }
}

?>
