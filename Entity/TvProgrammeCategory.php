<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        26.12.2015
 */
namespace BiberLtd\Bundle\TvManagementBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tv_programme_category", options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"})
 */
class TvProgrammeCategory extends CoreLocalizableEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=5)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

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
    public  $date_removed;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategoryLocalization",
     *     mappedBy="category"
     * )
     * @var array
     */
    protected $localizations;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory
     */
    private $parent;

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTimezone(){
        return $this->timezone;
    }

    /**
     * @param $timezone
     *
     * @return $this
     */
    public function setTimezone($timezone){
        if(!$this->setModified('timezone', $timezone)->isModified()){
            return $this;
        }
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNow(){
        return $this->now;
    }

    /**
     * @param $now
     *
     * @return $this
     */
    public function setNow($now){
        if(!$this->setModified('now', $now)->isModified()){
            return $this;
        }
        $this->now = $now;

        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory
     */
    public function getParent(){
        return $this->parent;
    }

    /**
     * @param \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory $parent
     *
     * @return $this
     */
    public function setParent(\BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory $parent){
        if(!$this->setModified('parent', $parent)->isModified()){
            return $this;
        }
        $this->parent = $parent;

        return $this;
    }
}