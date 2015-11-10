<?php
/**
 * @vendor      BiberLtd
 * @package		Core\Bundles\TvManagementBundle
 * @subpackage	Services
 * @name	    TvManagementBundle
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        10.11.2015
 */

namespace BiberLtd\Bundle\TvManagementBundle\Services;

/** Extends CoreModel */
use BiberLtd\Bundle\CoreBundle\CoreModel;

/** Required for better & instant error handling for the support team */
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;
use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;
use BiberLtd\Bundle\TvManagementBundle\Exceptions as BundleExceptions;

/** Entities to be used */
use BiberLtd\Bundle\TvManagementBundle\Entity as BundleEntity;

class TvManagementModel extends CoreModel{
    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.1.2
     *
     * @param           object          $kernel
     * @param           string          $dbConnection  Database connection key as set in app/config.yml
     * @param           string          $orm            ORM that is used.
     */
    public function __construct($kernel, $dbConnection = 'default', $orm = 'doctrine'){
        parent::__construct($kernel, $dbConnection, $orm);

        /**
         * Register entity names for easy reference.
         */
        $this->entity = array(
            'tvc'       => array('name' => 'TvManagementBundle:TvChannel', 'alias' => 'tvc'),
            'tvp'       => array('name' => 'TvManagementBundle:TvProgramme', 'alias' => 'tvp'),
            'tvpc'      => array('name' => 'TvManagementBundle:TvProgrammeCategory', 'alias' => 'tvpc'),
            'tvpcl'     => array('name' => 'TvManagementBundle:TvProgrammeCategoryLocalization', 'alias' => 'tvpcl'),
            'tvpg'      => array('name' => 'TvManagementBundle:TvProgrammeGenre', 'alias' => 'tvpg'),
            'tvpgl'     => array('name' => 'TvManagementBundle:TvProgrammeGenreLocalization', 'alias' => 'tvpgl'),
        );
    }
    /**
     * @name            __destruct()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct(){
        foreach($this as $property => $value) {
            $this->$property = null;
        }
    }
	/**
	 * @name 			deleteSite()
	 *
	 * @since			1.0.0
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->deleteSites()
	 *
	 * @param           mixed           $site
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteSite($site){
		return $this->deleteSites(array($site));
	}
    /**
     * @name 			deleteSites()
     *
     * @since			1.0.0
     * @version         1.0.8
	 *
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
	 *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
	public function deleteSites($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\Site){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getSite($entry);
				if(!$response->error->exists){
					$entry = $response->result->set;
					$this->em->remove($entry);
					$countDeleted++;
				}
			}
		}
		if($countDeleted < 0){
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
		}
		$this->em->flush();

		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
	}
	/**
	 * @name 			doesSiteExist()
	 *
	 * @since			1.0.0
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->getSite()
	 *
	 * @param           mixed           $site           Site entity or site id.
	 * @param           bool            $bypass         If set to true does not return response but only the result.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function doesSiteExist($site, $bypass = false) {
		$timeStamp = time();
		$exist = false;

		$response = $this->getSite($site);

		if ($response->error->exists) {
			if($bypass){
				return $exist;
			}
			$response->result->set = false;
			return $response;
		}

		$exist = true;

		if ($bypass) {
			return $exist;
		}
		return new ModelResponse(true, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			getDefaultLanguageOfSite()
	 *
	 * @since			1.0.6
	 * @version         1.1.3
	 *
	 * @author          Can Berkol
	 * @author          Said İmamoğlu
	 *
	 * @use             $this->getSite()
	 *
	 * @param           mixed           $site
	 * @param           bool            $bypass
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
    public function getDefaultLanguageOfSite($site, $bypass = false){
        $timeStamp = time();
        $response = $this->getSite($site);
        if($response->error->exist){
            return $response;
        }
        $language = $response->result->set->getDefaultLanguage();
        if(is_null($language)){
            return new ModelResponse(null, 1, 0, null, error, 'E:S:005', 'Default language is not set.', $timeStamp, time());
        }

        $lModel= $this->kernel->getContainer()->get('multilanguagesupport.model');
        $lResponse = $lModel->getLanguage($language);
        if ($lResponse->error->exist) {
            return $lResponse;
        }
        $language = $lResponse->result->set;
        if($bypass){
            return $language;
        }
        return new ModelResponse($language, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }
	/**
	 * @name 			getSite()
	 *
	 * @since			1.0.0
	 * @version         1.1.2
	 * @author          Can Berkol
	 *
	 * @use				$this->createException();
	 *
	 * @param           mixed           $site           Site entity or site id.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getSite($site) {
		$timeStamp = time();
		if($site instanceof BundleEntity\Site){
			return new ModelResponse($site, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
		}
		$result = null;
		switch($site){
			case is_numeric($site):
				$result = $this->em->getRepository($this->entity['s']['name'])->findOneBy(array('id' => $site));
				break;
			case is_string($site):
				$result = $this->em->getRepository($this->entity['s']['name'])->findOneBy(array('url_key' => $site));
				if(is_null($result)){
					$response = $this->getSiteByDomain($site);
					if(!$response->error->exist){
						$result = $response->result->set;
					}
				}
				break;
		}
		if(is_null($result)){
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			getSiteByDomain()
	 *
	 * @since			1.0.7
	 * @version         1.1.2
	 * @author          Can Berkol
	 *
	 * @use				$this->createException()
	 *
	 * @param           string			$domain
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getSiteByDomain($domain){
		$timeStamp = time();
		if (!is_string($domain)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be string.', 'E:S:004');
		}
		$result = $this->em->getRepository($this->entity['s']['name'])->findOneBy(array('domain' => $domain));
		if(is_null($result)){
			$response = $this->getSiteOfDomainAlias($domain);
			if(!$response->error->exist){
				$result = $response->result->set;
			}
		}
		if(is_null($result)){
			return new ModelResponse($result, 1, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}
		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			getSiteOfDomainAlias()
	 *
	 * @since			1.1.2
	 * @version         1.1.2
	 * @author          Can Berkol
	 *
	 * @use				$this->createException()
	 *
	 * @param           string			$alias
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getSiteOfDomainAlias($alias){
		$timeStamp = time();
		if (!is_string($alias)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be string.', 'E:S:004');
		}
		$result = $this->em->getRepository($this->entity['da']['name'])->findOneBy(array('domain' => $alias));

		if(is_null($result) || $result->getSite() == null){
			return new ModelResponse($result, 1, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}
		$site = $result->getSite();
		return new ModelResponse($site, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			getSiteSettings()
	 *
	 * @since			1.0.0
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->getSite()
	 *
	 * @param           mixed           $site
	 * @param           bool            $bypass
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getSiteSettings($site, $bypass = false){
		$timeStamp = time();
		$response = $this->getSite($site);

		if($response->error->exists){
			return $response;
		}

		$site = $response->result->set;

		$settings = $site->getSettings();

		if($bypass){
			return $settings;
		}

		return new ModelResponse($settings, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());

	}
	/**
	 * @name 			listDomainAliasesOfSite()
	 *
	 * @since			1.1.2
	 * @version         1.1.2
	 * @author          Can Berkol
	 *
	 * @param           mixed			$site
	 * @param           array           $filter
	 * @param           array           $sortOrder
	 * @param           array           $limit
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listDomainAliasesOfSite($site, $filter = null, $sortOrder = null, $limit = null) {
		$timeStamp = time();
		$response = $this->getSite($site);
		if($response->error->exist){
			return $response;
		}
		$site = $response->result->set;
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '.$this->entity['da']['alias']
			.' FROM '.$this->entity['da']['name'].' '.$this->entity['da']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'domain':
						$column = $this->entity['da']['alias'].'.'.$column;
						break;
					default:
						break;
				}
				$oStr .= ' '.$column.' '.strtoupper($direction).', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY '.$oStr.' ';
		}

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(

				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['da']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
				),
			)
		);

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE '.$fStr;
		}

		$qStr .= $wStr.$gStr.$oStr;

		$query = $this->em->createQuery($qStr);
		$query = $this->addLimit($query, $limit);

		$result = $query->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
    /**
     * @name 			listSites()
     *  				List registered sites from database based on a variety of conditions.
     *
     * @since			1.0.0
     * @version         1.0.8
     * @author          Can Berkol
     *
     * @param           array           $filter
     * @param           array           $sortOrder
     * @param           array           $limit
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
	public function listSites($filter = null, $sortOrder = null, $limit = null) {
		$timeStamp = time();
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';


		$qStr = 'SELECT '.$this->entity['s']['alias']
			.' FROM '.$this->entity['s']['name'].' '.$this->entity['s']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'title':
					case 'url_key':
					case 'date_added':
					case 'date_updated':
					case 'date_removed':
						$column = $this->entity['s']['alias'].'.'.$column;
						break;
					default:
						break;
				}
				$oStr .= ' '.$column.' '.strtoupper($direction).', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY '.$oStr.' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE '.$fStr;
		}

		$qStr .= $wStr.$gStr.$oStr;

		$query = $this->em->createQuery($qStr);
		$query = $this->addLimit($query, $limit);

		$result = $query->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			insertSite()
	 *
	 * @since			1.0.0
	 * @version         1.0.8
	 * @author          Can Berkol
	 *
	 * @use             $this->insertSites()
	 *
	 * @param           mixed           $site
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertSite($site){
		return $this->insertSites(array($site));
	}
    /**
     * @name 			insertSites()
     *
     * @since			1.0.0
     * @version         1.0.8
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection      Collection of Site entities or array of site detais array.
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
	public function insertSites($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
		foreach($collection as $data){
			if($data instanceof BundleEntity\Site){
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if(is_object($data)){
				$entity = new BundleEntity\Site();
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
						case 'default_language':
							$lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
							$response = $lModel->getLanguage($value);
							if(!$response->error->exists){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
		}
		if($countInserts > 0){
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
	/**
	 * @name 			updateSite()
	 *  				Update one or more sites into database.
	 *
	 * @since			1.0.0
	 * @version         1.0.3
	 * @author          Can Berkol
	 *
	 * @use             $this->updateSites()
	 *
	 * @param           array           $site      Site Entity or a collection of post input that stores site details.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateSite($site){
		return $this->updateSites(array($site));
	}
    /**
     * @name 			updateSites()
     *
     * @since			1.0.0
     * @version         1.0.8
     * @author          Can Berkol
     *
     * @ue              $this->createException()
     *
     * @param           array           $collection
     *
     * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateSites($collection){
        $timeStamp = time();
        /** Parameter must be an array */
        if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach($collection as $data){
            if($data instanceof BundleEntity\Site){
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            }
            else if(is_object($data)){
                if(!property_exists($data, 'id') || !is_numeric($data->id)){
					return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" parameter and id parameter must have an integer value.', 'E:S:003');
                }
                if(!property_exists($data, 'date_updated')){
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if(!property_exists($data, 'date_added')){
                    unset($data->date_added);
                }
                if(!property_exists($data, 'default_language')){
                    $data->default_language = 1;
                }
                $response = $this->getSite($data->id);
                if($response->error->exist){
					return $this->createException('EntityDoesNotExist', 'Site with id '.$data->id, 'E:D:002');
                }
                $oldEntity = $response->result->set;
                foreach($data as $column => $value){
                    $set = 'set'.$this->translateColumnName($column);
                    switch($column){
                        case 'default_language':
                            $lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                            $response = $lModel->getLanguage($value);
                            if(!$response->error->exist){
								$oldEntity->$set($response->result->set);
                            }
                            else{
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if($oldEntity->isModified()){
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            }
        }
		if($countUpdates > 0){
			$this->em->flush();
		return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
    }
}
/**
 * Change Log
 * **************************************
 * v1.1.3                      22.07.2015
 * Can Berkol
 * **************************************
 * BF :: getDefaultLanguageOfSite() invalid method call fixed.
 *
 * **************************************
 * v1.1.2                      14.07.2015
 * Can Berkol
 * **************************************
 * FR :: 3806788 :: getSiteOfDomainAlias() added.
 * FR :: 3806788 :: listDomainAliasesOfSite() added.
 *
 * **************************************
 * v1.1.1                      30.06.2015
 * Said İmamoğlu
 * **************************************
 * BF :: getDefaultLanguageOfSite() was returning only int. It replaced with language entity.
 *
 * **************************************
 * v1.1.0                      24.06.2015
 * Can Berkol
 * **************************************
 * BF :: listSites() has an invalid alias "l". It is replaced with "s".
 *
 * **************************************
 * v1.0.9                      25.05.2015
 * Can Berkol
 * **************************************
 * BF :: db_connection is replaced with dbConnection
 * BF :: Use statement for ModelResponse has been added to header.
 *
 * **************************************
 * v1.0.8                      01.05.2015
 * Can Berkol
 * **************************************
 * CR :: Made compatible with BiberLtd\Bundle\CoreBundle v3.3.
 *
 * **************************************
 * v1.0.7                      28.04.2015
 * TW #
 * Can Berkol
 * **************************************
 * A getSiteByDomain()
 * U getSite()
 *
 * **************************************
 * v1.0.6                   Said İmamoğlu
 * 21.02.2014
 * **************************************
 * A getDefaultLanguage()
 * 
 * **************************************
 * v1.0.5                      Can Berkol
 * 20.02.2014
 * **************************************
 * U insertSites()
 * U updateSites()
 *
 * **************************************
 * v1.0.4                      Can Berkol
 * 25.01.2014
 * **************************************
 * B updateSite()
 * U updateSites()
 *
 * **************************************
 * v1.0.3                      Can Berkol
 * 07.11.2013
 * **************************************
 * M The class now extends CoreModel
 * M Methods now use $this->createException() method.
 * M Method names are now camelCase.
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 16.08.2013
 * **************************************
 * B list_sites() NULL filter query problem fixed.
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 05.08.2013
 * **************************************
 * B delete_sites() Query parameter was not being set.
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 03.08.2013
 * **************************************
 * A __construct()
 * A __destruct()
 * A delete_site()
 * A delete_sites()
 * A does_site_exist()
 * A getSite()
 * A getSite_settings()
 * A insert_site()
 * A insert_sites()
 * A list_sites()
 * A update_site()
 * A update_sites()
 */