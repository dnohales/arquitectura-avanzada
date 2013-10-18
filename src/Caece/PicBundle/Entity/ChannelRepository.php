<?php
namespace Caece\PicBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ChannelRepository
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class ChannelRepository extends EntityRepository
{
    public function findActiveChannels()
    {
        return $this->findBy(array(
            'active' => true
        ));
    }
}
