<?php
namespace Caece\PicBundle\Settings;

/**
 * Description of ChannelSettings
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class ChannelSetting
{
    /*
     * @var int
     */
    private $number;
    
    /**
     * @var string
     */
    private $sensorClassName;
    
    /**
     * @var bool
     */
    private $active;
    
    /**
     * @var int
     */
    private $beginThreshold;
    
    /**
     * @var int
     */
    private $endThreshold;
    
    /**
     * Método conveniente para crear una lectura para el presente canal.
     * 
     * @param type $rawData El dato leído
     * @return ChannelReading La lectura
     */
    public function createReading($rawData)
    {
        $reading = new ChannelReading();
        $reading->setChannel($this->number);
        $reading->setRawData($rawData);
        
        return $reading;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }
    
    public function getSensorClassName()
    {
        return $this->sensorClassName;
    }

    public function setSensorClassName($sensorClassName)
    {
        $this->sensorClassName = $sensorClassName;
    }
    
    /**
     * @return SensorInterface El sensor
     * configurado para este canal
     */
    public function getSensor()
    {
        return SensorManager::getInstance()->getSensorByClassName($this->getSensorClassName());
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }
    
    public function getBeginThreshold()
    {
        return $this->beginThreshold;
    }

    public function setBeginThreshold($beginThreshold)
    {
        $this->beginThreshold = $beginThreshold;
    }

    public function getEndThreshold()
    {
        return $this->endThreshold;
    }

    public function setEndThreshold($endThreshold)
    {
        $this->endThreshold = $endThreshold;
    }

}
