<?php
/**
 * @name        TvProgrammeGenre
 * @package     BiberLtd\Bundle\TvManagementBundle\Entity
 *
 * @author      Can Berkol
 * @version     1.0.0
 * @date        10.11.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
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
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_added;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_removed;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenreLocalization",
     *     mappedBy="genre"
     * )
     */
    private $localizations;

	/**
	 * @name        getId ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getId(){
		return $this->id;
	}

}