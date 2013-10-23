<?php
namespace Caece\PicBundle\Settings;

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
     * @Assert\Range({
     *     "min" = 0,
     *     "max" = 2359
     * })
     */
    private $lightStartTime;
    
    /**
     * @var string
     * 
     * @Assert\Range({
     *     "min" = 0,
     *     "max" = 2359
     * })
     */
    private $lightEndTime;
    
    /**
     * @var bool 
     */
    private $lightScheduleEnabled;
    
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

}
