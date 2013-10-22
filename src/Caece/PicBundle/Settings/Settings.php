<?php
namespace Caece\PicBundle\Settings;

/**
 * Description of Settings
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class Settings
{
    /**
     * @var string
     */
    private $notifyEmailAddress;
    
    /**
     * @var int
     */
    private $sampleInterval;
    
    /**
     * @var ChannelSettingCollection
     */
    private $channels;
    
    /**
     * @var \DateTime
     */
    private $lightStartTime;
    
    /**
     * @var \DateTime
     */
    private $lightEndTime;
    
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

    public function getLightStartTime()
    {
        return $this->lightStartTime;
    }

    public function setLightStartTime(\DateTime $lightStartTime)
    {
        $this->lightStartTime = $lightStartTime;
    }

    public function getLightEndTime()
    {
        return $this->lightEndTime;
    }

    public function setLightEndTime(\DateTime $lightEndTime)
    {
        $this->lightEndTime = $lightEndTime;
    }

}
