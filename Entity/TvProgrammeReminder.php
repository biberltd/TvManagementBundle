<?php
namespace BiberLtd\Bundle\TvManagementBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tv_programme_reminder", options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"})
 */
class TvProgrammeReminder extends CoreEntity
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var  \DateTime
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
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $date_reminder;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme")
     * @ORM\JoinColumn(name="programme", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme
     */
    private $programme;

	/**
	 * @return \DateTime
	 */
    public function getDateReminder(){
        return $this->date_reminder;
    }

	/**
	 * @param \DateTime $date_reminder
	 *
	 * @return $this
	 */
    public function setDateReminder(\DateTime $date_reminder){
        if(!$this->setModified('date_reminder', $date_reminder)->isModified()){
            return $this;
        }
        $this->date_reminder = $date_reminder;

        return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
	 */
    public function getMember(){
        return $this->member;
    }

	/**
	 * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
	 *
	 * @return $this
	 */
    public function setMember(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member){
        if(!$this->setModified('member', $member)->isModified()){
            return $this;
        }
        $this->member = $member;

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