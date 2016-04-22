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
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $broadcast_type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=155, nullable=true)
     */
    private $motto;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $rating_tag;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $presenter;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $broadcast_quality;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $production_year;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $is_dubbed;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $is_turkish;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $raw_json;

    /**
     * 
     */
    private $uniqe_key;
}

/**
 * 
 * 
 */
class TvProgramme extends CoreEntity
{
    /**
     * 
     * 
     * 
     */
    private $id;

    /**
     * 
     */
    private $summary;

    /**
     * 
     */
    private $date_added;

    /**
     * 
     */
    private $date_updated;

    /**
     * 
     */
    private $date_removed;

    /**
     * 
     */
    private $title_original;

    /**
     * 
     */
    private $title_local;

    /**
     * 
     */
    private $broadcast_type;

    /**
     * 
     */
    private $description;

    /**
     * 
     */
    private $motto;

    /**
     * 
     */
    private $rating_tag;

    /**
     * 
     */
    private $url;

    /**
     * 
     */
    private $presenter;

    /**
     * 
     */
    private $broadcast_quality;

    /**
     * 
     */
    private $production_year;

    /**
     * 
     */
    private $is_dubbed;

    /**
     * 
     */
    private $is_turkish;

    /**
     * 
     */
    private $raw_json;

    /**
     *
     */
    private $uniqe_key;

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id){
        if(!$this->setModified('id', $id)->isModified()){
            return $this;
        }
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary(){
        return $this->summary;
    }

    /**
     * @param $summary
     *
     * @return $this
     */
    public function setSummary($summary){
        if(!$this->setModified('summary', $summary)->isModified()){
            return $this;
        }
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleOriginal(){
        return $this->title_original;
    }

    /**
     * @param $title_original
     *
     * @return $this
     */
    public function setTitleOriginal($title_original){
        if(!$this->setModified('title_original', $title_original)->isModified()){
            return $this;
        }
        $this->title_original = $title_original;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleLocal(){
        return $this->title_local;
    }

    /**
     * @param $title_local
     *
     * @return $this
     */
    public function setTitleLocal($title_local){
        if(!$this->setModified('title_local', $title_local)->isModified()){
            return $this;
        }
        $this->title_local = $title_local;

        return $this;
    }

    /**
     * @return string
     */
    public function getBroadcastType(){
        return $this->broadcast_type;
    }

    /**
     * @param $broadcast_type
     *
     * @return $this
     */
    public function setBroadcastType($broadcast_type){
        if(!$this->setModified('broadcast_type', $broadcast_type)->isModified()){
            return $this;
        }
        $this->broadcast_type = $broadcast_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description){
        if(!$this->setModified('description', $description)->isModified()){
            return $this;
        }
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMotto(){
        return $this->motto;
    }

    /**
     * @param $motto
     *
     * @return $this
     */
    public function setMotto($motto){
        if(!$this->setModified('motto', $motto)->isModified()){
            return $this;
        }
        $this->motto = $motto;

        return $this;
    }

    /**
     * @return string
     */
    public function getRatingTag(){
        return $this->rating_tag;
    }

    /**
     * @param $rating_tag
     *
     * @return $this
     */
    public function setRatingTag($rating_tag){
        if(!$this->setModified('rating_tag', $rating_tag)->isModified()){
            return $this;
        }
        $this->rating_tag = $rating_tag;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url){
        if(!$this->setModified('url', $url)->isModified()){
            return $this;
        }
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getPresenter(){
        return $this->presenter;
    }

    /**
     * @param $presenter
     *
     * @return $this
     */
    public function setPresenter($presenter){
        if(!$this->setModified('presenter', $presenter)->isModified()){
            return $this;
        }
        $this->presenter = $presenter;

        return $this;
    }

    /**
     * @return string
     */
    public function getBroadcastQuality(){
        return $this->broadcast_quality;
    }

    /**
     * @param $broadcast_quality
     *
     * @return $this
     */
    public function setBroadcastQuality($broadcast_quality){
        if(!$this->setModified('broadcast_quality', $broadcast_quality)->isModified()){
            return $this;
        }
        $this->broadcast_quality = $broadcast_quality;

        return $this;
    }

    /**
     * @return int
     */
    public function getProductionYear(){
        return $this->production_year;
    }

    /**
     * @param $production_year
     *
     * @return $this
     */
    public function setProductionYear($production_year){
        if(!$this->setModified('production_year', $production_year)->isModified()){
            return $this;
        }
        $this->production_year = $production_year;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsDubbed(){
        return $this->is_dubbed;
    }

    /**
     * @param $is_dubbed
     *
     * @return $this
     */
    public function setIsDubbed($is_dubbed){
        if(!$this->setModified('is_dubbed', $is_dubbed)->isModified()){
            return $this;
        }
        $this->is_dubbed = $is_dubbed;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsTurkish(){
        return $this->is_turkish;
    }

    /**
     * @param $is_turkish
     *
     * @return $this
     */
    public function setIsTurkish($is_turkish){
        if(!$this->setModified('is_turkish', $is_turkish)->isModified()){
            return $this;
        }
        $this->is_turkish = $is_turkish;

        return $this;
    }

    /**
     * @return string
     */
    public function getRawJson(){
        return $this->raw_json;
    }

    /**
     * @param $raw_json
     *
     * @return $this
     */
    public function setRawJson($raw_json){
        if(!$this->setModified('raw_json', $raw_json)->isModified()){
            return $this;
        }
        $this->raw_json = $raw_json;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUniqeKey(){
        return $this->uniqe_key;
    }

    /**
     * @param $uniqe_key
     *
     * @return $this
     */
    public function setUniqeKey($uniqe_key){
        if(!$this->setModified('uniqe_key', $uniqe_key)->isModified()){
            return $this;
        }
        $this->uniqe_key = $uniqe_key;

        return $this;
    }


}