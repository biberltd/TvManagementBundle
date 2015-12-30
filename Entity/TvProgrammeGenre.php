<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        28.12.2015
 */
namespace BiberLtd\Bundle\TvManagementBundle\Entity;
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tv_programme_genre", options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"})
 */
class TvProgrammeGenre extends CoreLocalizableEntity
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
     *     targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenreLocalization",
     *     mappedBy="genre"
     * )
     * @var array
     */
    public $localizations;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre
     */
    private $parent;

	/**
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * @return \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre
	 */
	public function getParent(){
		return $this->parent;
	}

	/**
	 * @param \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre $parent
	 *
	 * @return $this
	 */
	public function setParent(\BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre $parent){
		if(!$this->setModified('parent', $parent)->isModified()){
			return $this;
		}
		$this->parent = $parent;

		return $this;
	}
}