<?php
namespace Caece\PicBundle\Settings;

use Caece\PicBundle\Entity\ChannelReading;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of Settings
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class Settings
{
    /**
     * @var string
     * 
     * @Assert\Email()
     */
    private $notifyEmailAddress;
    
    /**
     * @var int
     * 
     * @Assert\Range({
     *     "min" = 1,
     *     "max" = 100
     * })
     */
    private $sampleInterval;
    
    /**
     * @var ChannelSettingCollection
     */
    private $channels;
    
    /**
     * @var string
     * 
     * @Assert\Regex({
     *     "pattern" = "/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/",
     *     "message" = "El valor inicial no es un horario válido"
     * })
     */
    private $lightStartTime;
    
    /**
     * @var string
     * 
     * @Assert\Regex({
     *     "pattern" = "/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/",
     *     "message" = "El valor final no es un horario válido"
     * })
     */
    private $lightEndTime;
    
    /**
     * @var bool 
     */
    private $lightScheduleEnabled;
    
    /**
     * @var bool
     */
    private $showCharts;
    
    public function __construct()
    {
        $this->channels = new ChannelSettingCollection();
    }
    
    public function getNotifyEmailAddress()
    {
        return $this->notifyEmailAddress;
    }

    public function setNotifyEmailAddress($notifyEmailAddress)
    {
        $this->notifyEmailAddress = $notifyEmailAddress;
    }

    public function getSampleInterval()
    {
        return $this->sampleInterval;
    }

    public function setSampleInterval($sampleInterval)
    {
        $this->sampleInterval = $sampleInterval;
    }

    public function getChannels()
    {
        return $this->channels;
    }

    public function setChannels(ChannelSettingCollection $channels)
    {
        $this->channels = $channels;
    }
    
    /**
     * Devuelve el valor convertido de una lectura específica. Si el canal no
     * tiene configurado un sensor
     * 
     * @param \Caece\PicBundle\Entity\ChannelReading $reading
     * @return type
     */
    public function convertReading(ChannelReading $reading, $addUnit = false)
    {
        $channel = $this->getChannels()->get($reading->getChannel());
        
        if ($channel === null || $channel->getSensor() === null) {
            return $reading->getRawData();
        } else {
            $result = $channel->getSensor()->convertRawData($reading->getRawData());
            if ($addUnit) {
                $result .= ' '.$channel->getSensor()->getUnitName();
            }
            return $result;
        }
    }

    public function getLightStartTime()
    {
        return $this->lightStartTime;
    }

    public function setLightStartTime($lightStartTime)
    {
        $this->lightStartTime = $lightStartTime;
    }

    public function getLightEndTime()
    {
        return $this->lightEndTime;
    }

    public function setLightEndTime($lightEndTime)
    {
        $this->lightEndTime = $lightEndTime;
    }
    
    public function getLightScheduleEnabled()
    {
        return $this->lightScheduleEnabled;
    }

    public function setLightScheduleEnabled($lightScheduleEnabled)
    {
        $this->lightScheduleEnabled = $lightScheduleEnabled;
    }
    
    public function getShowCharts()
    {
        return $this->showCharts;
    }

    public function setShowCharts($showCharts)
    {
        $this->showCharts = $showCharts;
    }

}
