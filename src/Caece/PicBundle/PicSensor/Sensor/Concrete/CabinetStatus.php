<?php
namespace Caece\PicBundle\PicSensor\Sensor\Concrete;

use Caece\PicBundle\PicSensor\Sensor\AbstractBooleanSensor;

/**
 * Description of CabinetStatus
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class CabinetStatus extends AbstractBooleanSensor
{
    public function getName()
    {
        return 'Estado del gabinete';
    }
    
    public function convertRawData($rawData)
    {
        return $rawData == 1? 'Abierto':'Cerrado';
    }
    
}
