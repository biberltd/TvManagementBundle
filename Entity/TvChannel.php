<?php
/**
 * @name        TvChannel
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
 * @ORM\Table(
 *     name="tv_channel",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","comment":"Holds a list of tv channels.","engine":"innodb"}
 * )
 */
class TvChannel extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $frequency;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
	public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
	public $date_removed;

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
	 * @name        getName()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @name        setName()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $name
	 *
	 * @return      $this
	 */
	public function setNameOriginal($name){
		if(!$this->setModified('name_original', $name)->isModified()){
			return $this;
		}
		$this->name = $name;

		return $this;
	}

	/**
	 * @name        getLogo ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getLogo(){
		return $this->logo;
	}

	/**
	 * @name        setLogo ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $logo
	 *
	 * @return      $this
	 */
	public function setLogo($logo){
		if(!$this->setModified('logo', $logo)->isModified()){
			return $this;
		}
		$this->logo = $logo;

		return $this;
	}

	/**
	 * @name        getFrequency ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @return      mixed
	 */
	public function getFrequency(){
		return $this->frequency;
	}

	/**
	 * @name        setFrequency ()
	 *
	 * @author      Can Berkol
	 *
	 * @since       1.0.0
	 * @version     1.0.0
	 *
	 * @param       mixed $frequency
	 *
	 * @return      $this
	 */
	public function setFrequency($frequency){
		if(!$this->setModified('frequency', $frequency)->isModified()){
			return $this;
		}
		$this->frequency = $frequency;

		return $this;
	}


}