<?php
namespace Caece\PicBundle\PicSensor\Sensor;

use Caece\PicBundle\PicSensor\Algorithm\AlgorthmInterface;

/**
 * Description of AbstractSensor
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
abstract class AbstractSensor implements SensorInterface
{
    protected $conversionAlgorithm;
    
    /**
     * Crea un algoritmo de conversión para que esta clase pueda implementar
     * el método convertRawData.
     * 
     * @return AlgorthmInterface El algoritmo de conversión.
     */
    protected abstract function createConversionAlgorithm();
    
    public function convertRawData($rawData)
    {
        if ($this->conversionAlgorithm === null) {
            $this->conversionAlgorithm = $this->createConversionAlgorithm();
        }
        
        return $this->conversionAlgorithm->rawToReal($rawData);
    }

}
