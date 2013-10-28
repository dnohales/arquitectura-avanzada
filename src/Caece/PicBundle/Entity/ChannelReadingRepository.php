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
    public function findFromDate(\DateTime $from)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.readedAt > :from')
            ->setParameter('from', $from)
            ->orderBy('r.readedAt', 'ASC')
            ->getQuery()->getResult();
    }
}
