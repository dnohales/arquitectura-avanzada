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
    
    public function findLatestByChannel()
    {
        $dql = <<<DQL
SELECT r1
FROM CaecePicBundle:ChannelReading r1
WHERE r1.readedAt = (
    SELECT MAX(r2.readedAt)
    FROM CaecePicBundle:ChannelReading r2
    WHERE r1.channel = r2.channel
)
ORDER BY r1.channel ASC
DQL;
        
        return $this->getEntityManager()
                    ->createQuery($dql)
                    ->getResult();
    }
    
    public function findByChannelBetweenDates($channel, \DateTime $beginTime, \DateTime $endTime)
    {
        return $this->createQueryBuilder('r')
                    ->andWhere('r.channel = :channel')
                    ->setParameter('channel', $channel)
                    ->andWhere('r.readedAt BETWEEN :beginTime AND :endTime')
                    ->setParameter('beginTime', $beginTime)
                    ->setParameter('endTime', $endTime)
                    ->orderBy('r.readedAt', 'ASC')
                    ->getQuery()->getResult();
    }
}
