<?php
namespace Caece\PicBundle\PicSensor\Sensor\Concrete;

use Caece\PicBundle\PicSensor\Sensor\AbstractBooleanSensor;

/**
 * Description of SolarCellStatus
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class SolarCellStatus extends AbstractBooleanSensor
{
    public function getName()
    {
        return 'Estado de la celda solar';
    }
    
    public function convertRawData($rawData)
    {
        return $rawData == 1? 'Sin energía':'Funcionando';
    }

}
