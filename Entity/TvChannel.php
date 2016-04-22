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
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $logo;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $frequency;

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
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $is_premium;

	/**
	 * @return mixed
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @param $name
	 *
	 * @return $this
	 */
	public function setNameOriginal($name){
		if(!$this->setModified('name_original', $name)->isModified()){
			return $this;
		}
		$this->name = $name;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLogo(){
		return $this->logo;
	}

	/**
	 * @param $logo
	 *
	 * @return $this
	 */
	public function setLogo($logo){
		if(!$this->setModified('logo', $logo)->isModified()){
			return $this;
		}
		$this->logo = $logo;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFrequency(){
		return $this->frequency;
	}

	/**
	 * @param $frequency
	 *
	 * @return $this
	 */
	public function setFrequency($frequency){
		if(!$this->setModified('frequency', $frequency)->isModified()){
			return $this;
		}
		$this->frequency = $frequency;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getIsPremium()
	{
		return $this->is_premium;
	}

	/**
	 * @param $is_premium
	 * @return $this
	 */
	public function setIsPremium($is_premium)
	{
		if (!$this->setModified('is_premium', $is_premium)->isModified()) {
			return $this;
		}
		$this->is_premium = $is_premium;
		return $this;
	}
	
}