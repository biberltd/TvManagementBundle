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
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="categories_of_tv_programme",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"}
 * )
 */
class CategoriesOfTvProgramme extends CoreEntity
{
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
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme")
     * @ORM\JoinColumn(name="programme", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgramme
     */
    private $programme;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory
     */
    private $category;

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

    /**
     * @return \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory
     */
    public function getCategory(){
        return $this->category;
    }

    /**
     * @param \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory $category
     *
     * @return $this
     */
    public function setCategory(\BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory $category){
        if(!$this->setModified('category', $category)->isModified()){
            return $this;
        }
        $this->category = $category;

        return $this;
    }


}