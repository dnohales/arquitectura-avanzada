<?php
namespace Caece\PicBundle\Entity;

use Caece\PicBundle\PicSensor\Sensor\SensorInterface;
use Caece\PicBundle\PicSensor\Sensor\SensorManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Channel
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 * 
 * @ORM\Entity(repositoryClass="ChannelRepository")
 */
class Channel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;
    
    /**
     * @ORM\Column(type="smallint")
     *
     * @var int
     */
    private $hardwareChannelNumber;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $sensorClassName;
    
    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $active;
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Método conveniente para crear una lectura para el presente canal.
     * 
     * @param type $rawData El dato leído
     * @return ChannelReading La lectura
     */
    public function createReading($rawData)
    {
        $reading = new ChannelReading();
        $reading->setChannel($this);
        $reading->setRawData($rawData);
        
        return $reading;
    }

    public function getHardwareChannelNumber()
    {
        return $this->hardwareChannelNumber;
    }

    public function setHardwareChannelNumber($hardwareChannelNumber)
    {
        $this->hardwareChannelNumber = $hardwareChannelNumber;
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

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

}
