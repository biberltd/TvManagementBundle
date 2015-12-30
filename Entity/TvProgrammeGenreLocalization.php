<?php
/**
 * @name        TvProgrammeGenreLocalization
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
 *     name="tv_programme_genre_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"}
 * )
 */
class TvProgrammeGenreLocalization extends CoreEntity
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
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre",
     *     inversedBy="localizations"
     * )
     * @ORM\JoinColumn(name="genre", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre
     */
    private $genre;

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
    public function getLanguage(){
        return $this->language;
    }

    /**
     * @param $language
     *
     * @return $this
     */
    public function setLanguage($language){
        if(!$this->setModified('language', $language)->isModified()){
            return $this;
        }
        $this->language = $language;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGenre(){
        return $this->genre;
    }

    /**
     * @param \BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre $genre
     *
     * @return $this
     */
    public function setGenre(\BiberLtd\Bundle\TvManagementBundle\Entity\TvProgrammeGenre $genre){
        if(!$this->setModified('genre', $genre)->isModified()){
            return $this;
        }
        $this->genre = $genre;

        return $this;
    }

}