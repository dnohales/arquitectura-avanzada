<?php
namespace Caece\PicBundle\PicSensor\Sensor\Concrete;

use Caece\PicBundle\PicSensor\Sensor\SensorInterface;

/**
 * Description of DummySensor
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class DummySensor implements SensorInterface
{
    public function getName()
    {
        return 'Sensor falso';
    }

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
        return $rawData;
    }
}
