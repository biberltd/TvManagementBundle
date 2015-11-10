<?php
/**
 * @name        TvProgramme
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
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tv_programme", options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"})
 */
class TvProgramme

{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="time", nullable=false)
     */
    private $time;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $rating;

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
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $title_original;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title_local;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvChannel")
     * @ORM\JoinColumn(name="channel", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre")
     * @ORM\JoinColumn(name="genre", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $genre;

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

    /**
     * @name        getTime ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getTime(){
        return $this->time;
    }

    /**
     * @name        setTime ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $time
     *
     * @return      $this
     */
    public function setTime($time){
        if(!$this->setModified('time', $time)->isModified()){
            return $this;
        }
        $this->time = $time;

        return $this;
    }

    /**
     * @name        getSummary ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getSummary(){
        return $this->summary;
    }

    /**
     * @name        setSummary ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $summary
     *
     * @return      $this
     */
    public function setSummary($summary){
        if(!$this->setModified('summary', $summary)->isModified()){
            return $this;
        }
        $this->summary = $summary;

        return $this;
    }

    /**
     * @name        getRating ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getRating(){
        return $this->rating;
    }

    /**
     * @name        setRating ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $rating
     *
     * @return      $this
     */
    public function setRating($rating){
        if(!$this->setModified('rating', $rating)->isModified()){
            return $this;
        }
        $this->rating = $rating;

        return $this;
    }

    /**
     * @name        getChannel ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getChannel(){
        return $this->channel;
    }

    /**
     * @name        setChannel ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $channel
     *
     * @return      $this
     */
    public function setChannel($channel){
        if(!$this->setModified('channel', $channel)->isModified()){
            return $this;
        }
        $this->channel = $channel;

        return $this;
    }

    /**
     * @name        getCategory ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getCategory(){
        return $this->category;
    }

    /**
     * @name        setCategory ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $category
     *
     * @return      $this
     */
    public function setCategory($category){
        if(!$this->setModified('category', $category)->isModified()){
            return $this;
        }
        $this->category = $category;

        return $this;
    }

    /**
     * @name        getGenre ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      mixed
     */
    public function getGenre(){
        return $this->genre;
    }

    /**
     * @name        setGenre ()
     *
     * @author      Can Berkol
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       mixed $genre
     *
     * @return      $this
     */
    public function setGenre($genre){
        if(!$this->setModified('genre', $genre)->isModified()){
            return $this;
        }
        $this->genre = $genre;

        return $this;
    }

	/**
	 * @name        getTitleOriginal ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getTitleOriginal(){
		return $this->title_original;
	}

	/**
	 * @name        setTitleOriginal ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $title_original
	 *
	 * @return      $this
	 */
	public function setTitleOriginal($title_original){
		if(!$this->setModified('title_original', $title_original)->isModified()){
			return $this;
		}
		$this->title_original = $title_original;

		return $this;
	}

	/**
	 * @name        getTitleLocal ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getTitleLocal(){
		return $this->title_local;
	}

	/**
	 * @name        setTitleLocal ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $title_local
	 *
	 * @return      $this
	 */
	public function setTitleLocal($title_local){
		if(!$this->setModified('title_local', $title_local)->isModified()){
			return $this;
		}
		$this->title_local = $title_local;

		return $this;
	}

}