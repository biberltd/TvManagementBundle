<?php
namespace BiberLtd\Bundle\TvManagementBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tv_programme_schedule",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUTvProgrammeSchedule", columns={"actual_time","channel","programme"})}
 * )
 */
class TvProgrammeSchedule extends CoreEntity
{
    /**
     * @ORM\Column(type="integer", unique=true, nullable=true, options={"default":2})
     * @var string
     */
    private $utc_offset;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":2})
     * @var string
     */
    private $gmt_offset;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    public $date_removed;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $actual_time;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $end_time;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0})
     * @var int
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvChannel")
     * @ORM\JoinColumn(name="channel", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvChannel
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme")
     * @ORM\JoinColumn(name="programme", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme
     */
    private $programme;

	/**
	 * @return string
	 */
	public function getUtcOffset(){
		return $this->utc_offset;
	}

	/**
	 * @param $utc_offset
	 *
	 * @return $this
	 */
	public function setUtcOffset($utc_offset){
		if(!$this->setModified('utc_offset', $utc_offset)->isModified()){
			return $this;
		}
		$this->utc_offset = $utc_offset;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getGmtOffset(){
		return $this->gmt_offset;
	}

	/**
	 * @param $gmt_offset
	 *
	 * @return $this
	 */
	public function setGmtOffset($gmt_offset){
		if(!$this->setModified('gmt_offset', $gmt_offset)->isModified()){
			return $this;
		}
		$this->gmt_offset = $gmt_offset;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getActualTime(){
		return $this->actual_time;
	}

	/**
	 * @param \DateTime $actual_time
	 *
	 * @return $this
	 */
	public function setActualTime(\DateTime $actual_time){
		if(!$this->setModified('actual_time', $actual_time)->isModified()){
			return $this;
		}
		$this->actual_time = $actual_time;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getEndTime(){
		return $this->end_time;
	}

	/**
	 * @param \DateTime $end_time
	 *
	 * @return $this
	 */
	public function setEndTime(\DateTime $end_time){
		if(!$this->setModified('end_time', $end_time)->isModified()){
			return $this;
		}
		$this->end_time = $end_time;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDuration(){
		return $this->duration;
	}

	/**
	 * @param $duration
	 *
	 * @return $this
	 */
	public function setDuration($duration){
		if(!$this->setModified('duration', $duration)->isModified()){
			return $this;
		}
		$this->duration = $duration;

		return $this;
	}

	/**
	 * @return \BiberLtd\Bundle\TvManagementBundle\Entity\TvChannel
	 */
	public function getChannel(){
		return $this->channel;
	}

	/**
	 * @param \BiberLtd\Bundle\TvManagementBundle\Entity\TvChannel $channel
	 *
	 * @return $this
	 */
	public function setChannel(\BiberLtd\Bundle\TvManagementBundle\Entity\TvChannel $channel){
		if(!$this->setModified('channel', $channel)->isModified()){
			return $this;
		}
		$this->channel = $channel;

		return $this;
	}

	/**
	 * @return \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme
	 */
	public function getProgramme(){
		return $this->programme;
	}

	/**
	 * @param \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme $programme
	 *
	 * @return $this
	 */
	public function setProgramme(\BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme $programme){
		if(!$this->setModified('programme', $programme)->isModified()){
			return $this;
		}
		$this->programme = $programme;

		return $this;
	}

}