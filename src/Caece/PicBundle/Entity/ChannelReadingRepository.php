<?php

namespace Caece\PicBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Description of ChannelReadingRepository
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 */
class ChannelReadingRepository extends EntityRepository
{
    public function findFromDate(DateTime $from)
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
    
    public function findByChannelBetweenDates($channel, DateTime $beginDate, DateTime $endDate, DateTime $beginTime, DateTime $endTime)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addEntityResult('CaecePicBundle:ChannelReading', 'r');
        $rsm->addFieldResult('r', 'id', 'id');
        $rsm->addFieldResult('r', 'readedAt', 'readedAt');
        $rsm->addFieldResult('r', 'rawData', 'rawData');
        $rsm->addFieldResult('r', 'channel', 'channel');
        
        $sql = <<<SQL
SELECT r.id, r.readedAt, r.rawData, r.channel
FROM ChannelReading r
WHERE r.channel = :channel AND
      r.readedAt BETWEEN :beginDate AND DATE_ADD(:endDate, INTERVAL 1 DAY) AND
      TIME(r.readedAt) BETWEEN TIME(:beginTime) AND TIME(:endTime)
SQL;
        
        return $this->getEntityManager()->createNativeQuery($sql, $rsm)
                    ->setParameter('channel', $channel)
                    ->setParameter('beginDate', $beginDate)
                    ->setParameter('endDate', $endDate)
                    ->setParameter('beginTime', $beginTime)
                    ->setParameter('endTime', $endTime)
                    ->getResult();
    }
}
