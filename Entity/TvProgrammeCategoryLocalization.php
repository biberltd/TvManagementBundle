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
use BiberLtd\Bundle\CoreBundle\CoreEntity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="tv_programme_category_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"}
 * )
 */
class TvProgrammeCategoryLocalization extends CoreEntity
{
    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $url_key;

    /**
     * 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory",
     *     inversedBy="localizations"
     * )
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @ORM\Id
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeCategory
     */
    private $category;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

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
	public function setName($name){
		if(!$this->setModified('name', $name)->isModified()){
			return $this;
		}
		$this->name = $name;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUrlKey(){
		return $this->url_key;
	}

	/**
	 * @param $url_key
	 *
	 * @return $this
	 */
	public function setUrlKey($url_key){
		if(!$this->setModified('url_key', $url_key)->isModified()){
			return $this;
		}
		$this->url_key = $url_key;

		return $this;
	}

	/**
	 * @return mixed
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

	/**
	 * @return \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
	 */
	public function getLanguage(){
		return $this->language;
	}

	/**
	 * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
	 *
	 * @return $this
	 */
	public function setLanguage(\BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language){
		if(!$this->setModified('language', $language)->isModified()){
			return $this;
		}
		$this->language = $language;

		return $this;
	}
}