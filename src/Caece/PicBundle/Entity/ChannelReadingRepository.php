<?php

namespace Caece\PicBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ChannelReadingRepository
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class ChannelReadingRepository extends EntityRepository
{
    public function findByChannelFromDate(Channel $channel, \DateTime $from = null)
    {
        //return $this->findBy($criteria, '');
    }
}
