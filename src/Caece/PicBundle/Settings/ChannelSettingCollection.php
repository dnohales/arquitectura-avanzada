<?php
namespace Caece\PicBundle\Settings;

use Caece\PicBundle\PicSensor\Sensor\SensorInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of ChannelSettingCollection
 *
 * @author Damian
 */
class ChannelSettingCollection extends ArrayCollection
{
    public function getActiveBooleans()
    {
        return $this->getByTypeAndState(SensorInterface::TYPE_BOOLEAN, true);
    }
    
    public function getActiveContinuous()
    {
        return $this->getByTypeAndState(SensorInterface::TYPE_CONTINUOUS, true);
    }
    
    public function getByTypeAndState($type, $state)
    {
        return $this->filter(function($channel) use ($type, $state) {
            return $channel->getSensor()->getType() === $type && $channel->isActive() === $state;
        });
    }
}

?>
