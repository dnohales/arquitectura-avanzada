<?php
namespace Caece\PicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of ChannelReading
 *
 * @author Damián Nohales <damiannohales@uxorit.com>
 * 
 * @ORM\Entity(repositoryClass="ChannelReadingRepository")
 */
class ChannelReading
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime la fecha y hora de cuando se realizó la lectura
     */
    private $readedAt;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int el canal leído
     */
    private $channel;

    /**
     * @ORM\Column(type="smallint")
     *
     * @var int los datos leídos en crudo, esto es, un número del 1 al 1024
     */
    private $rawData;

    public function __construct()
    {
        $this->readedAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getReadedAt()
    {
        return $this->readedAt;
    }

    public function setReadedAt(\DateTime $readedAt)
    {
        $this->readedAt = $readedAt;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }
}
