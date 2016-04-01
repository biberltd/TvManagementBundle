<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        28.12.2015
 */
namespace BiberLtd\Bundle\TvManagementBundle\Services;

use BiberLtd\Bundle\CoreBundle\CoreModel;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;
use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;
use BiberLtd\Bundle\TvManagementBundle\Exceptions as BundleExceptions;
use BiberLtd\Bundle\TvManagementBundle\Entity as BundleEntity;
use Doctrine\ORM\Query;
use TvdeBugun\Bundle\ApiBundle\ViewEntities\TvProgramme;

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
			'cotp'      => array('name' => 'TvManagementBundle:CategoriesOfTvProgramme', 'alias' => 'cotp'),
			'gotp'      => array('name' => 'TvManagementBundle:GenresOfTvProgramme', 'alias' => 'gotp'),
			'tvc'       => array('name' => 'TvManagementBundle:TvChannel', 'alias' => 'tvc'),
			'tvp'       => array('name' => 'TvManagementBundle:TvProgramme', 'alias' => 'tvp'),
			'tvpc'      => array('name' => 'TvManagementBundle:TvProgrammeCategory', 'alias' => 'tvpc'),
			'tvpcl'     => array('name' => 'TvManagementBundle:TvProgrammeCategoryLocalization', 'alias' => 'tvpcl'),
			'tvpg'      => array('name' => 'TvManagementBundle:TvProgrammeGenre', 'alias' => 'tvpg'),
			'tvpgl'     => array('name' => 'TvManagementBundle:TvProgrammeGenreLocalization', 'alias' => 'tvpgl'),
			'tvpr'      => array('name' => 'TvManagementBundle:TvProgrammeReminder', 'alias' => 'tvpr'),
			'tvps'      => array('name' => 'TvManagementBundle:TvProgrammeSchedule', 'alias' => 'tvps'),
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
	 * @name 			deleteTvChannel()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->deleteTvChannels()
	 *
	 * @param           mixed           $channel
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteTvChannel($channel){
		return $this->deleteTvChannels(array($channel));
	}
	/**
	 * @name 			deleteTvChannels()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 *
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteTvChannels($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\TvChannel){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getTvChannel($entry);
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
	 * @name 			doesTvChannelExist()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->getSite()
	 *
	 * @param           mixed           $channel        TvChannel entity or site id.
	 * @param           bool            $bypass         If set to true does not return response but only the result.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function doesTvChannelExist($channel, $bypass = false) {
		$timeStamp = time();
		$exist = false;

		$response = $this->getTvChannel($channel);

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
	 * @name 			getTvChannel()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use				$this->createException()
	 *
	 * @param           mixed           $channel           TvChannel entity or site id.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getTvChannel($channel) {
		$timeStamp = time();
		if($channel instanceof BundleEntity\TvChannel){
			return new ModelResponse($channel, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
		}
		$result = null;
		switch($channel){
			case is_numeric($channel):
				$result = $this->em->getRepository($this->entity['tvc']['name'])->findOneBy(array('id' => $channel));
				break;
		}
		if(is_null($result)){
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			listTvChannels()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @param           array           $filter
	 * @param           array           $sortOrder
	 * @param           array           $limit
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvChannels($filter = null, $sortOrder = null, $limit = null) {
		$timeStamp = time();
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';


		$qStr = 'SELECT '.$this->entity['tvc']['alias']
			.' FROM '.$this->entity['tvc']['name'].' '.$this->entity['tvc']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'name':
					case 'frequency':
					case 'date_added':
					case 'date_updated':
					case 'date_removed':
						$column = $this->entity['tvc']['alias'].'.'.$column;
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
	 * @name 			insertTvChannel()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->insertTvChannels()
	 *
	 * @param           mixed           $channel
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvChannel($channel){
		return $this->insertTvChannels(array($channel));
	}
	/**
	 * @name 			insertTvChannels()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection      Collection of Site entities or array of site detais array.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvChannels($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
		foreach($collection as $data){
			if($data instanceof BundleEntity\TvChannel){
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if(is_object($data)){
				$entity = new BundleEntity\TvChannel();
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
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
	 * @name 			updateTvChannel()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->updateSites()
	 *
	 * @param           array           $channel      TvChannel Entity or a collection of post input that stores site details.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateTvChannel($channel){
		return $this->updateTvChannels(array($channel));
	}
	/**
	 * @name 			updateTvChannels()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @ue              $this->createException()
	 *
	 * @param           array           $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateTvChannels($collection){
		$timeStamp = time();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countUpdates = 0;
		$updatedItems = array();
		foreach($collection as $data){
			if($data instanceof BundleEntity\TvChannel){
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
				$response = $this->getTvChannel($data->id);
				if($response->error->exist){
					return $response;
				}
				$oldEntity = $response->result->set;
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
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

	/**
	 * @name 			deleteTvProgrammeCategory()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->deleteTvProgrammeCategories()
	 *
	 * @param           mixed           $category
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteTvProgrammeCategory($category){
		return $this->deleteTvProgrammeCategories(array($category));
	}
	/**
	 * @name 			deleteTvProgrammeCategories()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 *
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteTvProgrammeCategories($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\TvProgrammeCategory){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getTvProgrammeCategory($entry);
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
	 * @name 			doesTvProgrammeCategoryExist()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->getSite()
	 *
	 * @param           mixed           $category       TvProgrammeCategory entity or site id.
	 * @param           bool            $bypass         If set to true does not return response but only the result.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function doesTvProgrammeCategoryExist($category, $bypass = false) {
		$timeStamp = time();
		$exist = false;

		$response = $this->getTvProgrammeCategory($category);

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
	 * @name 			getTvProgrammeCategory()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use				$this->createException()
	 *
	 * @param           mixed           $category           TvProgrammeCategory entity or site id.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getTvProgrammeCategory($category){
		$timeStamp = time();
		if ($category instanceof BundleEntity\TvProgrammeCategory) {
			return new ModelResponse($category, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
		}
		$result = null;
		switch ($category) {
			case is_numeric($category):
				$result = $this->em->getRepository($this->entity['tvpc']['name'])->findOneBy(array('id' => $category));
				break;
			case is_string($category):
				$response = $this->getTvProgrammeCategoryByUrlKey($category);
				if (!$response->error->exist) {
					$result = $response->result->set;
				}

				unset($response);
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name            getTvProgrammeCategoryByUrlKey()
	 *
	 * @since           1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->listTvProgrammeCategories()
	 * @use             $this->createException()
	 *
	 * @param           mixed $urlKey
	 * @param           mixed $language
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getTvProgrammeCategoryByUrlKey($urlKey, $language = null){
		$timeStamp = time();
		if (!is_string($urlKey)) {
			return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvpcl']['alias'] . '.url_key', 'comparison' => '=', 'value' => $urlKey),
				)
			)
		);
		if (!is_null($language)) {
			$mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
			$response = $mModel->getLanguage($language);
			if (!$response->error->exist) {
				$filter[] = array(
					'glue' => 'and',
					'condition' => array(
						array(
							'glue' => 'and',
							'condition' => array('column' => $this->entity['tvpcl']['alias'] . '.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
						)
					)
				);
			}
		}
		$response = $this->listTvProgrammeCategories($filter, null, array('start' => 0, 'count' => 1));

		$response->result->set = $response->result->set[0];
		$response->stats->execution->start = $timeStamp;
		$response->stats->execution->end = time();

		return $response;
	}
	/**
	 * @name 			listTvProgrammeCategories()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @param           array           $filter
	 * @param           array           $sortOrder
	 * @param           array           $limit
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammeCategories($filter = null, $sortOrder = null, $limit = null)
	{
		$timeStamp = time();
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '. $this->entity['tvpcl']['alias']
			. ' FROM ' . $this->entity['tvpcl']['name'] . ' ' . $this->entity['tvpcl']['alias']
			. ' JOIN ' . $this->entity['tvpcl']['alias'] . '.category ' . $this->entity['tvpc']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'date_added':
					case 'date_updated':
					case 'date_removed':
						$column = $this->entity['tvpcl']['alias'] . '.' . $column;
						break;
					case 'name':
					case 'url_key':
						$column = $this->entity['tvpcl']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);
		$result = $q->getResult();
		$entities = array();
		$unique = array();
		foreach ($result as $entry) {
			$id = $entry->getCategory()->getId();
			if (!isset($unique[$id])) {
				$entities[] = $entry->getCategory();
				$unique[$id] = $entry->getCategory();
			}
		}
		$totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			insertTvProgrammeCategory()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->insertTvProgrammes()
	 *
	 * @param           mixed           $category
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammeCategory($category){
		return $this->insertTvProgrammeCategories(array($category));
	}
	/**
	 * @name 			insertTvProgrammeCategories()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection      Collection of TvProgrammeCategory entities or array of site detais array.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammeCategories($collection){
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$countLocalizations = 0;
		$insertedItems = array();
		$localizations = array();
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\TvProgrammeCategory) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\TvProgrammeCategory;
				if (!property_exists($data, 'date_added')) {
					$data->date_added = $now;
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = $now;
				}
				foreach ($data as $column => $value) {
					$localeSet = false;
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'parent':
							$response = $this->getTvProgrammeCategory($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response);
							break;
						case 'local':
							$localizations[$countInserts]['localizations'] = $value;
							$localeSet = true;
							$countLocalizations++;
							break;
						default:
							$entity->$set($value);
							break;
					}
					if ($localeSet) {
						$localizations[$countInserts]['entity'] = $entity;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		/** Now handle localizations */
		if ($countInserts > 0 && $countLocalizations > 0) {
			$response = $this->insertTvProgrammeCategoryLocalizations($localizations);
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
	/**
	 * @name            insertTvProgrammeCategoryLocalizations ()
	 *
	 * @since           1.1.8
	 * @version         1.5.5
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammeCategoryLocalizations($collection){
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\TvProgrammeCategoryLocalization) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else {
				$cat = $data['entity'];
				foreach ($data['localizations'] as $locale => $translation) {
					$entity = new BundleEntity\TvProgrammeCategoryLocalization();
					$lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
					$response = $lModel->getLanguage($locale);
					if ($response->error->exist) {
						return $response;
					}
					$entity->setLanguage($response->result->set);
					unset($response);
					$entity->setCategory($cat);
					foreach ($translation as $column => $value) {
						$set = 'set' . $this->translateColumnName($column);
						switch ($column) {
							default:
								if (is_object($value) || is_array($value)) {
									$value = json_encode($value);
								}
								$entity->$set($value);
								break;
						}
					}
					$this->em->persist($entity);
					$insertedItems[] = $entity;
					$countInserts++;
				}
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
	/**
	 * @name 			updateTvProgrammeCategory()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->updateSites()
	 *
	 * @param           mixed           $category      TvProgrammeCategory Entity or a collection of post input that stores site details.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateTvProgrammeCategory($category){
		return $this->updateTvProgrammeCategories(array($category));
	}
	/**
	 * @name 			updateTvProgrammeCategories()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @ue              $this->createException()
	 *
	 * @param           array           $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateTvProgrammeCategories($collection){
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countUpdates = 0;
		$updatedItems = array();
		$localizations = array();
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\TvProgrammeCategory) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				$response = $this->getTvProgrammeCategory($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Category with id / url_key ' . $data->id . ' does not exist in database.', 'E:D:002');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'local':
							foreach ($value as $langCode => $translation) {
								$localization = $oldEntity->getLocalization($langCode, true);
								$newLocalization = false;
								if (!$localization) {
									$newLocalization = true;
									$localization = new BundleEntity\TvProgrammeCategoryLocalization();
									$mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
									$response = $mlsModel->getLanguage($langCode);
									$localization->setLanguage($response->result->set);
									$localization->setCategory($oldEntity);
								}
								foreach ($translation as $transCol => $transVal) {
									$transSet = 'set' . $this->translateColumnName($transCol);
									$localization->$transSet($transVal);
								}
								if ($newLocalization) {
									$this->em->persist($localization);
								}
								$localizations[] = $localization;
							}
							$oldEntity->setLocalizations($localizations);
							break;
						case 'id':
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if ($oldEntity->isModified()) {
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
	}
	/**
	 * @name 			deleteTvProgramme()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->deleteTvChannels()
	 *
	 * @param           mixed           $programme
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteTvProgramme($programme){
		return $this->deleteTvProgrammes(array($programme));
	}
	/**
	 * @name 			deleteTvProgrammes()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 *
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteTvProgrammes($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\TvProgramme){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getTvProgramme($entry);
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
	 * @name 			doesTvProgrammeExist()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->getSite()
	 *
	 * @param           mixed           $programme      Tvrogramme entity or site id.
	 * @param           bool            $bypass         If set to true does not return response but only the result.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function doesTvProgrammeExist($programme, $bypass = false) {
		$timeStamp = time();
		$exist = false;

		$response = $this->getTvProgramme($programme);

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
	 * @name 			getTvProgramme()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use				$this->createException()
	 *
	 * @param           mixed           $programme           TvProgramme entity or site id.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getTvProgramme($programme) {
		$timeStamp = time();
		if($programme instanceof BundleEntity\TvProgramme){
			return new ModelResponse($programme, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
		}
		$result = null;
		switch($programme){
			case is_numeric($programme):
				$result = $this->em->getRepository($this->entity['tvp']['name'])->findOneBy(array('id' => $programme));
				break;
			case is_string($programme):
				$result = $this->em->getRepository($this->entity['tvp']['name'])->findOneBy(array('uniq_key' => $programme));
				break;
		}
		if(is_null($result)){
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			listTvProgrammes()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @param           array           $filter
	 * @param           array           $sortOrder
	 * @param           array           $limit
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammes($filter = null, $sortOrder = null, $limit = null) {
		$timeStamp = time();
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';
		$qStr = 'SELECT '.$this->entity['tvp']['alias']
			.' FROM '.$this->entity['tvp']['name'].' '.$this->entity['tvp']['alias'];

		if (!is_null($sortOrder) && count($sortOrder) > 0) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'time':
					case 'title_original':
					case 'title_local':
					case 'date_added':
					case 'date_updated':
					case 'date_removed':
						$column = $this->entity['tvp']['alias'].'.'.$column;
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
	 * @name 			insertTvProgramme()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->insertTvProgrammes()
	 *
	 * @param           mixed           $programme
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgramme($programme){
		return $this->insertTvProgrammes(array($programme));
	}
	/**
	 * @name 			insertTvProgrammes()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection      Collection of Site entities or array of site detais array.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammes($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
		foreach($collection as $data){
			if($data instanceof BundleEntity\TvProgramme){
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if(is_object($data)){
				$entity = new BundleEntity\TvProgramme();
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
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
	 * @name 			insertTvProgrammeSchedule()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->insertTvProgrammes()
	 *
	 * @param           mixed           $schedule
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammeSchedule($schedule){
		return $this->insertTvProgrammeSchedules(array($schedule));
	}
	/**
	 * @name 			insertTvProgrammeSchedules()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection      Collection of Site entities or array of site detais array.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammeSchedules($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
		foreach($collection as $data){
			if($data instanceof BundleEntity\TvProgrammeSchedule){
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if(is_object($data)){
				$entity = new BundleEntity\TvProgrammeSchedule();
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
						case 'channel':
							$response = $this->getTvChannel($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response);
							break;
						case 'programme':
							$response = $this->getTvProgramme($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response);
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
	 * @name 			updateTvProgramme()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->updateSites()
	 *
	 * @param           mixed           $programme      TvProgramme Entity or a collection of post input that stores site details.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateTvProgramme($programme){
		return $this->updateTvProgrammes(array($programme));
	}
	/**
	 * @name 			updateTvProgrammes()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @ue              $this->createException()
	 *
	 * @param           array           $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateTvProgrammes($collection){
		$timeStamp = time();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countUpdates = 0;
		$updatedItems = array();
		foreach($collection as $data){
			if($data instanceof BundleEntity\TvProgramme){
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
				$response = $this->getTvProgramme($data->id);
				if($response->error->exist){
					return $response;
				}
				$oldEntity = $response->result->set;
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
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
	/**
	 * @name 			deleteTvProgrammeGenre()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->deleteTvProgrammeGenres()
	 *
	 * @param           mixed           $genre
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteTvProgrammeGenre($genre){
		return $this->deleteTvProgrammeGenres(array($genre));
	}
	/**
	 * @name 			deleteTvProgrammeGenres()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 *
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteTvProgrammeGenres($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\TvProgrammeGenre){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getTvProgrammeGenre($entry);
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
	 * @name 			doesTvProgrammeGenreExist()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->getSite()
	 *
	 * @param           mixed           $genre          TvProgrammeGenre entity or site id.
	 * @param           bool            $bypass         If set to true does not return response but only the result.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function doesTvProgrammeGenreExist($genre, $bypass = false) {
		$timeStamp = time();
		$exist = false;

		$response = $this->getTvProgrammeGenre($genre);

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
	 * @name 			getTvProgrammeGenre()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use				$this->createException()
	 *
	 * @param           mixed           $genre           TvProgrammeGenre entity or site id.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getTvProgrammeGenre($genre){
		$timeStamp = time();
		if ($genre instanceof BundleEntity\TvProgrammeGenre) {
			return new ModelResponse($genre, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
		}
		$result = null;
		switch ($genre) {
			case is_numeric($genre):
				$result = $this->em->getRepository($this->entity['tvpg']['name'])->findOneBy(array('id' => $genre));
				break;
			case is_string($genre):
				$response = $this->getTvProgrammeGenreByUrlKey($genre);
				if (!$response->error->exist) {
					$result = $response->result->set;
				}

				unset($response);
				break;
			default:
				echo "i am here";
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name            getTvProgrammeGenreByUrlKey()
	 *
	 * @since           1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->listTvProgrammeGenres()
	 * @use             $this->createException()
	 *
	 * @param           mixed $urlKey
	 * @param           mixed $language
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getTvProgrammeGenreByUrlKey($urlKey, $language = null){
		$timeStamp = time();
		if (!is_string($urlKey)) {
			return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvpgl']['alias'] . '.url_key', 'comparison' => '=', 'value' => $urlKey),
				)
			)
		);
		if (!is_null($language)) {
			$mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
			$response = $mModel->getLanguage($language);
			if (!$response->error->exist) {
				$filter[] = array(
					'glue' => 'and',
					'condition' => array(
						array(
							'glue' => 'and',
							'condition' => array('column' => $this->entity['tvpgl']['alias'] . '.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
						)
					)
				);
			}
		}
		$response = $this->listTvProgrammeGenres($filter, null, array('start' => 0, 'count' => 1));

		$response->result->set = $response->result->set[0];
		$response->stats->execution->start = $timeStamp;
		$response->stats->execution->end = time();

		return $response;
	}
	/**
	 * @name 			listTvProgrammeGenres()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @param           array           $filter
	 * @param           array           $sortOrder
	 * @param           array           $limit
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammeGenres($filter = null, $sortOrder = null, $limit = null)
	{
		$timeStamp = time();
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '. $this->entity['tvpgl']['alias']
			. ' FROM ' . $this->entity['tvpgl']['name'] . ' ' . $this->entity['tvpgl']['alias']
			. ' JOIN ' . $this->entity['tvpgl']['alias'] . '.genre ' . $this->entity['tvpg']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'date_added':
					case 'date_updated':
					case 'date_removed':
						$column = $this->entity['tvpgl']['alias'] . '.' . $column;
						break;
					case 'name':
					case 'url_key':
						$column = $this->entity['tvpgl']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);
		$result = $q->getResult();
		$entities = array();
		$unique = array();
		foreach ($result as $entry) {
			$id = $entry->getGenre()->getId();
			if (!isset($unique[$id])) {
				$entities[] = $entry->getGenre();
				$unique[$id] = $entry->getGenre();
			}
		}
		$totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			insertTvProgrammeGenre()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->insertTvProgrammeGenres()
	 *
	 * @param           mixed           $genre
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammeGenre($genre){
		return $this->insertTvProgrammeGenres(array($genre));
	}
	/**
	 * @name 			insertTvProgrammeGenres()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array           $collection      Collection of TvProgrammeCategory entities or array of site detais array.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammeGenres($collection){
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$countLocalizations = 0;
		$insertedItems = array();
		$localizations = array();
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\TvProgrammeGenre) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\TvProgrammeGenre();
				if (!property_exists($data, 'date_added')) {
					$data->date_added = $now;
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = $now;
				}
				foreach ($data as $column => $value) {
					$localeSet = false;
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'local':
							$localizations[$countInserts]['localizations'] = $value;
							$localeSet = true;
							$countLocalizations++;
							break;
						case 'parent':
							$response = $this->getTvProgrammeGenre($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response);
							break;
						default:
							$entity->$set($value);
							break;
					}
					if ($localeSet) {
						$localizations[$countInserts]['entity'] = $entity;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		/** Now handle localizations */
		if ($countInserts > 0 && $countLocalizations > 0) {
			$response = $this->insertTvProgrammeGenreLocalizations($localizations);
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
	/**
	 * @name            insertTvProgrammeGenreLocalizations ()
	 *
	 * @since           1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->createException()
	 *
	 * @param           array $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertTvProgrammeGenreLocalizations($collection){
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\TvProgrammeGenreLocalization) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else {
				$cat = $data['entity'];
				foreach ($data['localizations'] as $locale => $translation) {
					$entity = new BundleEntity\TvProgrammeGenreLocalization();
					$lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
					$response = $lModel->getLanguage($locale);
					if ($response->error->exist) {
						return $response;
					}
					$entity->setLanguage($response->result->set);
					unset($response);
					$entity->setCategory($cat);
					foreach ($translation as $column => $value) {
						$set = 'set' . $this->translateColumnName($column);
						switch ($column) {
							default:
								if (is_object($value) || is_array($value)) {
									$value = json_encode($value);
								}
								$entity->$set($value);
								break;
						}
					}
					$this->em->persist($entity);
					$insertedItems[] = $entity;
					$countInserts++;
				}
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
	/**
	 * @name 			updateTvProgrammeGenre()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @use             $this->updateTvProgrammeGenres()
	 *
	 * @param           mixed           $genre      TvProgrammeGenre Entity or a collection of post input that stores site details.
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateTvProgrammeGenre($genre){
		return $this->updateTvProgrammeGenres(array($genre));
	}
	/**
	 * @name 			updateTvProgrammeGenres()
	 *
	 * @since			1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @ue              $this->createException()
	 *
	 * @param           array           $collection
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateTvProgrammeGenres($collection){
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countUpdates = 0;
		$updatedItems = array();
		$localizations = array();
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\TvProgrammeGenre) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				$response = $this->getTvProgrammeGenre($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Category with id / url_key ' . $data->id . ' does not exist in database.', 'E:D:002');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'local':
							foreach ($value as $langCode => $translation) {
								$localization = $oldEntity->getLocalization($langCode, true);
								$newLocalization = false;
								if (!$localization) {
									$newLocalization = true;
									$localization = new BundleEntity\TvProgrammeGenreLocalization();
									$mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
									$response = $mlsModel->getLanguage($langCode);
									$localization->setLanguage($response->result->set);
									$localization->setGenre($oldEntity);
								}
								foreach ($translation as $transCol => $transVal) {
									$transSet = 'set' . $this->translateColumnName($transCol);
									$localization->$transSet($transVal);
								}
								if ($newLocalization) {
									$this->em->persist($localization);
								}
								$localizations[] = $localization;
							}
							$oldEntity->setLocalizations($localizations);
							break;
						case 'id':
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if ($oldEntity->isModified()) {
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
	}

	/**
	 * @param      $tvProgrammeId
	 * @param null $sortOrder
	 * @param null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammeSchedulesOfProgramme($tvProgrammeId, $sortOrder = null, $limit = null) {
		$column = $this->entity['tvps']['alias'] . '.programme';
		$condition = array('column' => $column, 'comparison' => '=', 'value' => $tvProgrammeId);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);

		return $this->listTvProgrammeSchedules($filter, $sortOrder, $limit, true);
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 * @param bool       $distinct
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammeSchedules(array $filter = null, array $sortOrder = null, array $limit = null, $distinct = false){
		$timeStamp = time();
		$oStr = $wStr = $gStr = $fStr = '';
		if($distinct){
			$qStr = 'SELECT DISTINCT('. $this->entity['tvps']['alias'].'.programme), '.$this->entity['tvps']['alias']
				. ' FROM ' . $this->entity['tvps']['name'] . ' ' . $this->entity['tvps']['alias'];
		}
		else {
			$qStr = 'SELECT ' . $this->entity['tvps']['alias']
				. ' FROM ' . $this->entity['tvps']['name'] . ' ' . $this->entity['tvps']['alias'];
		}

		if (!is_null($sortOrder) && count($sortOrder)>0) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'date_added':
					case 'date_updated':
					case 'date_removed':
					case 'actual_time':
					case 'end_time':
					case 'duration':
						$column = $this->entity['tvps']['alias'] . '.' . $column;
						break;
				}

				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}
		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);
		if($distinct){
			/**
			 * @var Query $q
			 */
			$result = $q->getScalarResult();
		}
		else {
			$result = $q->getResult();
		}


		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param      $channel
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannel($channel, $filter = null, $sortOrder = null, $limit = null)	{
		return $this->listScheduledTvProgrammesOfChannels([$channel], $filter, $sortOrder, $limit);
	}

	/**
	 * @param array $channels
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannels(array $channels, $filter = null, $sortOrder = null, $limit = null)	{
		$timeStamp = time();

		$channelIds = [];
		foreach($channels as $channel) {
			$response = $this->getTvChannel($channel);
			if($response->error->exist){
				continue;
			}
			$channel = $response->result->set;
			unset($response);
			$channelIds[] = $channel->getId();
		}


		$column = $this->entity['tvps']['alias'] . '.channel';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $channelIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		foreach($sortOrder as $key => $value){
			switch($key){
				case 'actual_time':
				case 'end_time':
				case 'duration':
					$tvpsSortOrder = array($key => $value);
					break;
				default:
					$tvpSortOrder = array($key => $value);
					break;
			}
		}
		unset($sortOrder);
		$response = $this->listTvProgrammeSchedules(null, $tvpsSortOrder, null, true);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$pIds = [];
		foreach($response->result->set as $item){
			$pIds[] = $item->getProgramme()->getId();
		}
		array_pop($filter);
		unset($response);
		$column = $this->entity['tvp']['alias'] . '.id';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $pIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$response = $this->listTvProgrammes($filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$totalRows = count($response->result->set);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($response->result->set, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param \DateTime  $dateStart
	 * @param \DateTime  $dateEnd
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listScheduledTvProgrammesBetween(\DateTime $dateStart, \DateTime $dateEnd, array $filter = null, array $sortOrder = null, array $limit = null)	{
		$timeStamp = time();

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '>=', 'value' => $dateStart->format('Y-m-d H:i:s')),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '<=', 'value' => $dateEnd->format('Y-m-d H:i:s')),
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		foreach($sortOrder as $key => $value){
			switch($key){
				case 'actual_time':
				case 'end_time':
				case 'duration':
					$tvpsSortOrder = array($key => $value);
					break;
				default:
					$tvpSortOrder = array($key => $value);
					break;
			}
		}
		unset($sortOrder);
		$response = $this->listTvProgrammeSchedules(null, $tvpsSortOrder, null, true);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$pIds = [];
		foreach($response->result->set as $item){
			$pIds[] = $item->getProgramme()->getId();
		}
		array_pop($filter);
		unset($response);
		$column = $this->entity['tvp']['alias'] . '.id';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $pIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$response = $this->listTvProgrammes($filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$totalRows = count($response->result->set);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($response->result->set, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param           $channel
	 * @param null      $filter
	 * @param null      $sortOrder
	 * @param null      $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannelBetween(\DateTime $dateStart, \DateTime $dateEnd, $channel, $filter = null, $sortOrder = null, $limit = null)	{
		return $this->listScheduledTvProgrammesOfChannelsBetween($dateStart, $dateEnd, [$channel], $filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param array $channels
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannelsBetween(\DateTime $dateStart, \DateTime $dateEnd, array $channels, $filter = null, $sortOrder = null, $limit = null)	{
		$timeStamp = time();
		$channelIds = [];

		foreach ($channels as $channel) {
			$response = $this->getTvChannel($channel);
			if($response->error->exist){
				continue;
			}
			$channel = $response->result->set;
			$channelIds[] = $channel->getId();
			unset($response);
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.channel', 'comparison' => 'in', 'value' => $channelIds),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '>=', 'value' => $dateStart->format('Y-m-d H:i:s')),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '<=', 'value' => $dateEnd->format('Y-m-d H:i:s')),
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		foreach($sortOrder as $key => $value){
			switch($key){
				case 'actual_time':
				case 'end_time':
				case 'duration':
					$tvpsSortOrder[$key] = $value;
					break;
				default:
					$tvpSortOrder[$key] = $value;
					break;
			}
		}
		unset($sortOrder);

		$response = $this->listTvProgrammeSchedules($filter, $tvpsSortOrder, null, true);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$pIds = [];

		foreach($response->result->set as $item){
			$pIds[] = $item['tvps_programme'];
		}
		array_pop($filter);
		unset($response);
		$column = $this->entity['tvp']['alias'] . '.id';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $pIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$response = $this->listTvProgrammes($filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$totalRows = count($response->result->set);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($response->result->set, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}


	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param           $channel
	 * @param           $category
	 * @param null      $filter
	 * @param null      $sortOrder
	 * @param null      $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannelInCategoryBetween(\DateTime $dateStart, \DateTime $dateEnd, $channel, $category, $filter = null, $sortOrder = null, $limit = null)	{
		return $this->listScheduledTvProgrammesOfChannelsInCategoryBetween($dateStart, $dateEnd, [$channel], $category, $filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param array $channels
	 * @param $category
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannelsInCategoryBetween(\DateTime $dateStart, \DateTime $dateEnd, array $channels, $category, $filter = null, $sortOrder = null, $limit = null)	{
		$timeStamp = time();
		$channelIds = [];
		foreach($channels as $channel) {
			$response = $this->getTvChannel($channel);
			if($response->error->exist){
				continue;
			}
			$channel = $response->result->set;
			$channelIds[] = $channel->getId();
		}


		$response = $this->getTvProgrammeCategory($category);
		if($response->error->exist){
			return $response;
		}
		$category = $response->result->set;

		unset($response);

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.channel', 'comparison' => 'in', 'value' => $channelIds),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '>=', 'value' => $dateStart->format('Y-m-d H:i:s')),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '<=', 'value' => $dateEnd->format('Y-m-d H:i:s')),
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		if(!is_null($sortOrder)){
			foreach($sortOrder as $key => $value){
				switch($key){
					case 'actual_time':
					case 'end_time':
					case 'duration':
						$tvpsSortOrder = array($key => $value);
						break;
					default:
						$tvpSortOrgder = array($key => $value);
						break;
				}
			}
		}

		unset($sortOrder);
		$response = $this->listTvProgrammeSchedules(null, $tvpsSortOrder, null, true);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$pIds = [];
		foreach($response->result->set as $item){
			$pIds[] = $item->getProgramme()->getId();
		}
		array_pop($filter);
		unset($response);
		$column = $this->entity['tvp']['alias'] . '.id';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $pIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$response = $this->listTvProgrammesOfCategory($category, $filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$totalRows = count($response->result->set);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($response->result->set, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param           $channel
	 * @param           $genre
	 * @param null      $filter
	 * @param null      $sortOrder
	 * @param null      $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannelInGenreBetween(\DateTime $dateStart, \DateTime $dateEnd, $channel, $genre, $filter = null, $sortOrder = null, $limit = null)	{
		return $this->listScheduledTvProgrammesOfChannelsInGenresBetween($dateStart, $dateEnd, [$channel], [$genre], $filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param array $channels
	 * @param array $genres
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannelsInGenresBetween(\DateTime $dateStart, \DateTime $dateEnd, array $channels, array $genres, $filter = null, $sortOrder = null, $limit = null)	{
		$timeStamp = time();
		$channelIds = [];
		$genreIds = [];

		foreach($channels as $channel) {
			$response = $this->getTvChannel($channel);
			if($response->error->exist){
				continue;
			}
			$channel = $response->result->set;
			$channelIds[] = $channel->getId();
		}

		foreach ($genres as $genre) {
			$response = $this->getTvProgrammeGenre($genre);
			if($response->error->exist){
				continue;
			}
			$genre = $response->result->set;
			$genreIds[] = $genre->getId();
		}

		unset($response);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.channel', 'comparison' => 'in', 'value' => $channelIds),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '>=', 'value' => $dateStart->format('Y-m-d H:i:s')),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '<=', 'value' => $dateEnd->format('Y-m-d H:i:s')),
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		foreach($sortOrder as $key => $value){
			switch($key){
				case 'actual_time':
				case 'end_time':
				case 'duration':
					$tvpsSortOrder[$key] = $value;
					break;
				default:
					$tvpSortOrder[$key] = $value;
					break;
			}
		}
		unset($sortOrder);
		$response = $this->listTvProgrammeSchedules($filter, $tvpsSortOrder, null, true);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$pIds = [];
		foreach($response->result->set as $item){
			$pIds[] = $item['tvps_programme'];
		}
		array_pop($filter);
		unset($response);
		$column = $this->entity['tvp']['alias'] . '.id';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $pIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$response = $this->listTvProgrammesOfGenres($genreIds, $filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$totalRows = count($response->result->set);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($response->result->set, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param           $channel
	 * @param           $category
	 * @param           $genre
	 * @param null      $filter
	 * @param null      $sortOrder
	 * @param null      $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannelInCatAndGenreBetween(\DateTime $dateStart, \DateTime $dateEnd, $channel, $category, $genre, $filter = null, $sortOrder = null, $limit = null){
		return $this->listScheduledTvProgrammesOfChannelsInCatAndGenresBetween($dateStart, $dateEnd, [$channel], [$category], [$genre], $filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param array $channels
	 * @param $category
	 * @param array $genres
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfChannelsInCatAndGenresBetween(\DateTime $dateStart, \DateTime $dateEnd, array $channels, $category, array $genres, $filter = null, $sortOrder = null, $limit = null){
		$timeStamp = time();
		$channelIds = [];
		$genreIds = [];
		foreach ($channels as $channel) {
			$response = $this->getTvChannel($channel);
			if($response->error->exist){
				continue;
			}
			$channel = $response->result->set;
			$channelIds[] = $channel->getId();
		}

		foreach ($genres as $genre) {
			$response = $this->getTvProgrammeGenre($genre);
			if($response->error->exist){
				continue;
			}
			$genre = $response->result->set;
			$genreIds[] = $genre->getId();
		}


		$response = $this->getTvProgrammeCategory($category);
		if($response->error->exist){
			return $response;
		}
		$category = $response->result->set;

		unset($response);

		$filter[] = array(
			'glue'      => 'and',
			'condition' => array(
				array(
					'glue'      => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'].'.channel', 'comparison' => 'in', 'value' => $channelIds),
				),
				array(
					'glue'      => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'].'.actual_time', 'comparison' => '>=', 'value' => $dateStart->format('Y-m-d H:i:s')),
				),
				array(
					'glue'      => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'].'.actual_time', 'comparison' => '<=', 'value' => $dateEnd->format('Y-m-d H:i:s')),
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		foreach($sortOrder as $key => $value){
			switch($key){
				case 'actual_time':
				case 'end_time':
				case 'duration':
					$tvpsSortOrder = array($key => $value);
					break;
				default:
					$tvpSortOrder = array($key => $value);
					break;
			}
		}
		unset($sortOrder);
		$response = $this->listTvProgrammesOfCategory($category, $filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;

			return $response;
		}
		$catCol = $response->result->set;
		$response = $this->listTvProgrammesOfGenres($genreIds, $filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;

			return $response;
		}
		$genCol = $response->result->set;

		$allCol = [];
		foreach($catCol as $aCatP){
			foreach($genCol as $aGenP){
				if($aCatP->getId() == $aGenP->getId){
					$allCol[] = $aCatP;
				}
			}
		}
		unset($catCol, $genCol);
		$totalRows = count($allCol);
		if($totalRows < 1){
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}

		return new ModelResponse($allCol, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param           $genre
	 * @param null      $filter
	 * @param null      $sortOrder
	 * @param null      $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listScheduledTvProgrammesOfGenreBetween(\DateTime $dateStart, \DateTime $dateEnd, $genre, $filter = null, $sortOrder = null, $limit = null)	{
		return $this->listScheduledTvProgrammesOfGenresBetween($dateStart, $dateEnd, [$genre], $filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param array $genres
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfGenresBetween(\DateTime $dateStart, \DateTime $dateEnd, array $genres, array $filter = null, array $sortOrder = null, $limit = null)	{
		$timeStamp = time();
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '>=', 'value' => $dateStart->format('Y-m-d H:i:s')),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '<=', 'value' => $dateEnd->format('Y-m-d H:i:s')),
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		if(!is_null($sortOrder)) {
			foreach($sortOrder as $key => $value){
				switch($key){
					case 'actual_time':
					case 'end_time':
					case 'duration':
						$tvpsSortOrder[$key] = $value;
						break;
					default:
						$tvpSortOrder[$key] = $value;
						break;
				}
			}
			unset($sortOrder);
		}

		$response = $this->listTvProgrammeSchedules(null, $tvpsSortOrder, null, true);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$pIds = [];


		foreach($response->result->set as $key=>$item){
			$pIds[] = $item['tvps_programme'];
		}
		array_pop($filter);
		unset($response);
		$column = $this->entity['tvp']['alias'] . '.id';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $pIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$response = $this->listTvProgrammesOfGenres($genres, $filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$totalRows = count($response->result->set);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($response->result->set, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param $category
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfCategoryBetween(\DateTime $dateStart, \DateTime $dateEnd, $category, array $filter = null, array $sortOrder = null, array $limit = null)	{
		return $this->listScheduledTvProgrammesOfCategoriesBetween($dateStart, $dateEnd, [$category], $filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param array $categories
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfCategoriesBetween(\DateTime $dateStart, \DateTime $dateEnd, array $categories, array $filter = null, array $sortOrder = null, array $limit = null)	{
		$timeStamp = time();

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '>=', 'value' => $dateStart->format('Y-m-d H:i:s')),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '<=', 'value' => $dateEnd->format('Y-m-d H:i:s')),
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		foreach($sortOrder as $key => $value){
			switch($key){
				case 'actual_time':
				case 'end_time':
				case 'duration':
					$tvpsSortOrder[$key] = $value;
					break;
				default:
					$tvpSortOrder[$key] = $value;
					break;
			}
		}
		unset($sortOrder);
		$response = $this->listTvProgrammeSchedules($filter, $tvpsSortOrder, null, true);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}

		$pIds = [];
		foreach($response->result->set as $item){
			$pIds[] = $item['tvps_programme'];
		}
		array_pop($filter);
		unset($response);
		$column = $this->entity['tvp']['alias'] . '.id';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $pIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$response = $this->listTvProgrammesOfCategories($categories, $filter, $tvpSortOrder, $limit);

		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$totalRows = count($response->result->set);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($response->result->set, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param $category
	 * @param $genre
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfCategoryAndGenreBetween(\DateTime $dateStart, \DateTime $dateEnd, $category, $genre, $filter = null, $sortOrder = null, $limit = null)	{
		return $this->listScheduledTvProgrammesOfCategoryAndGenresBetween($dateStart, $dateEnd, $category, [$genre], $filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime $dateStart
	 * @param \DateTime $dateEnd
	 * @param $category
	 * @param array $genres
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 * @return ModelResponse
	 */
	public function listScheduledTvProgrammesOfCategoryAndGenresBetween(\DateTime $dateStart, \DateTime $dateEnd, $category, array $genres, $filter = null, $sortOrder = null, $limit = null)	{
		$timeStamp = time();
		$genreIds = [];
		$response = $this->getTvProgrammeCategory($category);
		if($response->error->exist){
			return $response;
		}

		foreach($genres as $genre) {
			$category = $response->result->set;
			$response = $this->getTvProgrammeGenre($genre);
			if($response->error->exist){
				continue;
			}
			$genre = $response->result->set;
			$genreIds[] = $genre->getId();
		}

		unset($response);

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '>=', 'value' => $dateStart->format('Y-m-d H:i:s')),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvps']['alias'] . '.actual_time', 'comparison' => '<=', 'value' => $dateEnd->format('Y-m-d H:i:s')),
				)
			)
		);
		$tvpsSortOrder = array();
		$tvpSortOrder = array();
		foreach($sortOrder as $key => $value){
			switch($key){
				case 'actual_time':
				case 'end_time':
				case 'duration':
					$tvpsSortOrder = array($key => $value);
					break;
				default:
					$tvpSortOrder = array($key => $value);
					break;
			}
		}
		unset($sortOrder);
		$response = $this->listTvProgrammeSchedules(null, $tvpsSortOrder, null, true);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$pIds = [];
		foreach($response->result->set as $item){
			$pIds[] = $item->getProgramme()->getId();
		}
		array_pop($filter);
		unset($response);
		$column = $this->entity['gotp']['alias'] . '.genre';
		$condition = array('column' => $column, 'comparison' => 'in', 'value' => $pIds);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		$response = $this->listTvProgrammesOfCategory($category, $filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$catCol = $response->result->set;

		$response = $this->listTvProgrammesOfGenres($genreIds, $filter, $tvpSortOrder, $limit);
		if($response->error->exist){
			$response->stats->execution->start = $timeStamp;
			return $response;
		}
		$genCol = $response->result->set;

		$allCol = [];
		foreach($catCol as $aCatP){
			foreach($genCol as $aGenP){
				if($aCatP->getId() == $aGenP->getId){
					$allCol[] = $aCatP;
				}
			}
		}
		unset($catCol, $genCol);
		$totalRows = count($allCol);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($allCol, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param            $category
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammesOfCategory($category, array $filter = null, array $sortOrder = null, array $limit = null){
		return $this->listTvProgrammesOfCategories([$category], $filter, $sortOrder, $limit);
	}

	/**
	 * @param array $categories
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 * @return ModelResponse
	 */
	public function listTvProgrammesOfCategories(array $categories, array $filter = null, array $sortOrder = null, array $limit = null){
		$timeStamp = time();
		$categoryIds = [];
		foreach($categories as $category) {
			$response = $this->getTvProgrammeCategory($category);
			if ($response->error->exist) {
				continue;
			}
			if(is_int($category)) {
				$categoryIds[] = $category;
			}
			else {
				$categoryIds[] = $category->getId();
			}
		}

		$categoryIdStr = '('.implode(',', $categoryIds).')';

		$qStr = 'SELECT ' . $this->entity['cotp']['alias']
			. ' FROM ' . $this->entity['tvp']['name'] . ' ' . $this->entity['tvp']['alias']
			. ' JOIN ' . $this->entity['cotp']['name'] . ' ' . $this->entity['cotp']['alias']
			. ' WHERE ' . $this->entity['cotp']['alias'] . '.category IN ' . $categoryIdStr;

		$oStr = '';
		if ($sortOrder != null) {
			foreach ($sortOrder as $column => $direction) {
				$sorting = false;
				if (!in_array($column, array('name', 'url_key'))) {
					$sorting = true;
					switch ($column) {
						case 'id':
						case 'date_added':
						case 'date_updated':
						case 'date_removed':
						case 'date_removed':
						case 'title_original':
						case 'title_local':
						case 'production_year':
						case 'rating_tag':
						case 'presenter':
							$column = $this->entity['tvp']['alias'] . '.' . $column;
							break;
					}
					$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
				}
			}
			if ($sorting) {
				$oStr = rtrim($oStr, ', ');
				$oStr = ' ORDER BY ' . $oStr . ' ';
			}
		}
		$qStr .= $oStr;
		$query = $this->em->createQuery($qStr);
		$result = $query->getResult();
		$collection = array();
		foreach ($result as $item) {
			/**
			 * @var \BiberLtd\Bundle\TvManagementBundle\Entity\CategoriesOfTvProgramme $item
			 */

			$collection[$item->getProgramme()->getId()] = $item->getProgramme()->getId();
		}
		unset($result);
		if(count($collection) < 1){
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvp']['alias'] . '.id', 'comparison' => 'in', 'value' => $collection),
				)
			)
		);
		return $this->listTvProgrammes($filter, $sortOrder, $limit);
	}

	/**
	 * @param mixed $genre
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammesOfGenre($genre, array $filter = null, array $sortOrder = null, array $limit = null){
		return $this->listTvProgrammesOfGenres([$genre], $filter, $sortOrder, $limit);
	}

	/**
	 * @param array $genres
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 * @return ModelResponse
	 */
	public function listTvProgrammesOfGenres(array $genres, array $filter = null, array $sortOrder = null, array $limit = null){
		$timeStamp = time();
		$genreIds = [];
		foreach($genres as $genre) {
			$response = $this->getTvProgrammeGenre($genre);
			if($response->error->exist){
				continue;
			}
			$genre = $response->result->set;
			unset($response);
			$genreIds[] = $genre->getId();
		}

		$genreIdsStr = '('.implode(',', $genreIds).')';
		$qStr = 'SELECT ' . $this->entity['gotp']['alias']
			. ' FROM ' . $this->entity['gotp']['name'] . ' ' . $this->entity['gotp']['alias']
			. ' JOIN ' . $this->entity['gotp']['alias'] . '.programme ' . $this->entity['tvp']['alias']
			. ' WHERE ' . $this->entity['gotp']['alias'] . '.genre IN ' . $genreIdsStr;


		$oStr = '';
		if ($sortOrder != null && count($sortOrder) > 0) {
			foreach ($sortOrder as $column => $direction) {
				$sorting = false;
				if (!in_array($column, array('name', 'url_key'))) {
					$sorting = true;
					switch ($column) {
						case 'id':
						case 'date_added':
						case 'date_updated':
						case 'date_removed':
						case 'date_removed':
						case 'title_original':
						case 'title_local':
						case 'production_year':
						case 'rating_tag':
						case 'presenter':
							$column = $this->entity['tvp']['alias'] . '.' . $column;
							break;
					}
					$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
				}
			}
			if ($sorting) {
				$oStr = rtrim($oStr, ', ');
				$oStr = ' ORDER BY ' . $oStr . ' ';
			}
		}
		$qStr .= $oStr;
		$query = $this->em->createQuery($qStr);
		$result = $query->getResult();

		$collection = array();
		foreach ($result as $item) {
			/**
			 * @var \BiberLtd\Bundle\TvManagementBundle\Entity\CategoriesOfTvProgramme $item
			 */
			$collection[] = $item->getProgramme()->getId();
		}
		unset($result);
		if(count($collection) < 1){
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvp']['alias'] . '.id', 'comparison' => 'in', 'value' => $collection),
				)
			)
		);
		return $this->listTvProgrammes($filter, $sortOrder, $limit);
	}

	/**
	 * @param mixed $channel
	 * @param mixed $category
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function listTvProgrammesOfChannelInCategory($channel, $category, array $sortOrder = null, array $limit = null)	{
		$timeStamp = time();
		$response = $this->getTvChannel($channel);
		if ($response->error->exist) {
			return $response;
		}
		$channel = $response->result->set;
		$response = $this->getTvProgrammeCategory($category);
		if ($response->error->exist) {
			return $response;
		}
		$category = $response->result->set;

		$response = $this->listScheduledTvProgrammesOfChannel($channel, null, $sortOrder, $limit);
		if($response->error->exist){
			return false;
		}
		$pIds = array();

		foreach($response->result->set as $programme){
			$pIds[] = $programme->getId();
		}
		unset($response);

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['cotp']['alias'] . '.programme', 'comparison' => 'in', 'value' => $pIds),
				)
			)
		);

		$response = $this->listTvProgrammesOfCategory($category, $filter, $sortOrder, $limit);
		$response->stats->execution->start = $timeStamp;
		return $response;
	}

	/**
	 * @param \DateTime  $dateStart
	 * @param \DateTime  $dateEnd
	 * @param mixed $channel
	 * @param mixed $category
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function listTvProgrammesOfChannelInCategoryBetween(\DateTime $dateStart, \DateTime $dateEnd, $channel, $category, array $sortOrder = null, array $limit = null)	{
		$timeStamp = time();
		$response = $this->getTvChannel($channel);
		if ($response->error->exist) {
			return $response;
		}
		$channel = $response->result->set;
		$response = $this->getTvProgrammeCategory($category);
		if ($response->error->exist) {
			return $response;
		}
		$category = $response->result->set;

		$response = $this->listScheduledTvProgrammesOfChannelBetween($dateStart, $dateEnd, $channel, null, $sortOrder, $limit);
		if($response->error->exist){
			return false;
		}
		$pIds = array();

		foreach($response->result->set as $programme){
			$pIds[] = $programme->getId();
		}
		unset($response);

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['cotp']['alias'] . '.programme', 'comparison' => 'in', 'value' => $pIds),
				)
			)
		);

		$response = $this->listTvProgrammesOfCategory($category, $filter, $sortOrder, $limit);
		$response->stats->execution->start = $timeStamp;
		return $response;
	}
	/**
	 * @param mixed $channel
	 * @param mixed $genre
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function listTvProgrammesOfChannelInGenre($channel, $genre, array $sortOrder = null, array $limit = null)	{
		$timeStamp = time();
		$response = $this->getTvChannel($channel);
		if ($response->error->exist) {
			return $response;
		}
		$channel = $response->result->set;
		$response = $this->getTvProgrammeGenre($genre);
		if ($response->error->exist) {
			return $response;
		}
		$genre = $response->result->set;

		$response = $this->listScheduledTvProgrammesOfChannel($channel, null, $sortOrder, $limit);
		if($response->error->exist){
			return false;
		}
		$pIds = array();

		foreach($response->result->set as $programme){
			$pIds[] = $programme->getId();
		}
		unset($response);

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['gotp']['alias'] . '.programme', 'comparison' => 'in', 'value' => $pIds),
				)
			)
		);

		$response = $this->listTvProgrammesOfGenre($genre, $filter, $sortOrder, $limit);
		$response->stats->execution->start = $timeStamp;
		return $response;
	}

	/**
	 * @param \DateTime  $dateStart
	 * @param \DateTime  $dateEnd
	 * @param mixed $channel
	 * @param mixed $genre
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function listTvProgrammesOfChannelInGenreBetween(\DateTime $dateStart, \DateTime $dateEnd, $channel, $genre, array $sortOrder = null, array $limit = null)	{
		$timeStamp = time();
		$response = $this->getTvChannel($channel);
		if ($response->error->exist) {
			return $response;
		}
		$channel = $response->result->set;
		$response = $this->getTvProgrammeGenre($genre);
		if ($response->error->exist) {
			return $response;
		}
		$genre = $response->result->set;

		$response = $this->listScheduledTvProgrammesOfChannelBetween($dateStart, $dateEnd, $channel, null, $sortOrder, $limit);
		if($response->error->exist){
			return false;
		}
		$pIds = array();

		foreach($response->result->set as $programme){
			$pIds[] = $programme->getId();
		}
		unset($response);

		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['gotp']['alias'] . '.programme', 'comparison' => 'in', 'value' => $pIds),
				)
			)
		);

		$response = $this->listTvProgrammesOfGenre($genre, $filter, $sortOrder, $limit);
		$response->stats->execution->start = $timeStamp;
		return $response;
	}
	/**
	 * @name            listTvProgrammesOfChannelInCategoryAndGenre()
	 *
	 * @since           1.0.0
	 * @version         1.0.0
	 * @author          Can Berkol
	 *
	 * @uses            $this->listTvProgrammes()
	 *
	 * @param           mixed $channel
	 * @param           mixed $category
	 * @param           mixed $genre
	 * @param           array $sortOrder
	 * @param           array $limit
	 *
	 * @return          \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammesOfChannelInCategoryAndGenre($channel, $category, $genre, $sortOrder = null, $limit = null)	{
		$timeStamp = time();
		$response = $this->getTvChannel($channel);
		if ($response->error->exist) {
			return $response;
		}
		$channel = $response->result->set;
		$response = $this->getTvProgrammeGenre($genre);
		if ($response->error->exist) {
			return $response;
		}
		$genre = $response->result->set;
		$response = $this->getTvProgrammeCategory($category);
		if ($response->error->exist) {
			return $response;
		}
		$response = $this->listTvProgrammesOfChannelInCategory($channel, $category);
		if($response->error->exist){
			return $response->result->set;
		}
		$catPList = $response->result->set;
		$response = $this->listTvProgrammesOfChannelInGenre($genre, $category);
		if($response->error->exist){
			return $response->result->set;
		}
		$genPList = $response->result->set;

		$programmes = array();
		foreach($catPList as $cProgramme){
			foreach($genPList as $gProgramme){
				if ($cProgramme-getId() == $gProgramme->getId()){
					$programmes[] = $cProgramme;
				}
			}
		}
		$response->result->set = $programmes;
		$response->stats->execution->start = $timeStamp;
		$response->result->count->set = count($programmes);
		$response->result->count->total = count($programmes);
		return $response;
	}
	/**
	 * @param array $collection
	 * @param mixed $category
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function addProgrammesToCategory(array $collection, $category)
	{
		$timeStamp = time();
		$response = $this->getTvProgrammeCategory($category);
		if ($response->error->exist) {
			return $response;
		}
		$category = $response->result->set;
		$copCollection = array();
		$count = 0;
		$now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $programme) {
			$response = $this->getTvProgramme($programme);
			if ($response->error->exist) {
				return $response;
			}
			$entity = $response->result->set;
			/** Check if association exists */
			if ($this->isProgrammeAssociatedWithCategory($entity, $category, true)) {
				break;
			}

			$cop = new BundleEntity\CategoriesOfTvProgramme();
			$cop->setProgramme($entity)->setCategory($category)->setDateAdded($now);

			$this->em->persist($cop);
			$copCollection[] = $cop;
			$count++;
		}
		if ($count > 0) {
			$this->em->flush();
			return new ModelResponse($copCollection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
	/**
	 * @param array $collection
	 * @param mixed $genre
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function addProgrammesToGenre(array $collection, $genre)
	{
		$timeStamp = time();
		$response = $this->getTvProgrammeGenre($genre);
		if ($response->error->exist) {
			return $response;
		}
		$category = $response->result->set;
		$copCollection = array();
		$count = 0;
		$now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $programme) {
			$response = $this->getTvProgramme($programme);
			if ($response->error->exist) {
				return $response;
			}
			$entity = $response->result->set;
			/** Check if association exists */
			if ($this->isProgrammeAssociatedWithGenre($entity, $genre, true)) {
				break;
			}

			$cop = new BundleEntity\GenresOfTvProgramme();
			$cop->setProgramme($entity)->setGenre($category)->setDateAdded($now);

			$this->em->persist($cop);
			$copCollection[] = $cop;
			$count++;
		}
		if ($count > 0) {
			$this->em->flush();
			return new ModelResponse($copCollection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}

	/**
	 * @param mixed $programme
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function addProgrammeToCategories($programme, array $collection){
		$timeStamp = time();
		$response = $this->getTvProgramme($programme);
		if ($response->error->exist) {
			return $response;
		}
		$programme = $response->result->set;
		$processQueue = array();
		$count = 0;
		$now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $category) {
			$response = $this->getTvProgrammeCategory($category);
			if ($response->error->exist) {
				continue;
			}
			$category = $response->result->set;
			if ($this->isProgrammeAssociatedWithCategory($programme, $category, true)) {
				continue;
			}
			/** prepare object */
			$cop = new BundleEntity\CategoriesOfTvProgramme();
			$cop->setProgramme($programme)->setCategory($category)->setDateAdded($now);

			/** persist entry */
			$this->em->persist($cop);
			$processQueue[] = $cop;
			$count++;
		}
		if ($count > 0) {
			$this->em->flush();
			return new ModelResponse($processQueue, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
	/**
	 * @param mixed $programme
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function addProgrammeToGenres($programme, array $collection){
		$timeStamp = time();
		$response = $this->getTvProgramme($programme);
		if ($response->error->exist) {
			return $response;
		}
		$programme = $response->result->set;
		$processQueue = array();
		$count = 0;
		$now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $genre) {
			$response = $this->getTvProgrammeGenre($genre);
			if ($response->error->exist) {
				continue;
			}
			$genre = $response->result->set;
			if ($this->isProgrammeAssociatedWithGenre($programme, $genre, true)) {
				continue;
			}
			/** prepare object */
			$cop = new BundleEntity\GenresOfTvProgramme();
			$cop->setProgramme($programme)->setGenre($genre)->setDateAdded($now);

			/** persist entry */
			$this->em->persist($cop);
			$processQueue[] = $cop;
			$count++;
		}
		if ($count > 0) {
			$this->em->flush();
			return new ModelResponse($processQueue, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
	/**
	 * @param mixed $programme
	 * @param mixed $category
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function isProgrammeAssociatedWithCategory($programme, $category, $bypass = false){
		$timeStamp = time();
		$response = $this->getTvProgramme($programme);
		if ($response->error->exist) {
			return $response;
		}
		$programme = $response->result->set;

		$response = $this->getTvProgrammeCategory($category);
		if ($response->error->exist) {
			return $response;
		}
		$category = $response->result->set;
		$found = false;

		$qStr = 'SELECT COUNT(' . $this->entity['cotp']['alias'] . '.programme)'
			. ' FROM ' . $this->entity['cotp']['name'] . ' ' . $this->entity['cotp']['alias']
			. ' WHERE ' . $this->entity['cotp']['alias'] . '.programme = ' . $programme->getId()
			. ' AND ' . $this->entity['cotp']['alias'] . '.category = ' . $category->getId();
		$query = $this->em->createQuery($qStr);

		$result = $query->getSingleScalarResult();

		if ($result > 0) {
			$found = true;
		}
		if ($bypass) {
			return $found;
		}
		return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @param mixed $programme
	 * @param mixed $genre
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function isProgrammeAssociatedWithGenre($programme, $genre, $bypass = false){
		$timeStamp = time();
		$response = $this->getTvProgramme($programme);
		if ($response->error->exist) {
			return $response;
		}
		$programme = $response->result->set;

		$response = $this->getTvProgrammeGenre($genre);
		if ($response->error->exist) {
			return $response;
		}
		$genre = $response->result->set;
		$found = false;

		$qStr = 'SELECT COUNT(' . $this->entity['gotp']['alias'] . '.programme)'
			. ' FROM ' . $this->entity['gotp']['name'] . ' ' . $this->entity['gotp']['alias']
			. ' WHERE ' . $this->entity['gotp']['alias'] . '.programme = ' . $programme->getId()
			. ' AND ' . $this->entity['gotp']['alias'] . '.genre = ' . $genre->getId();
		$query = $this->em->createQuery($qStr);

		$result = $query->getSingleScalarResult();

		if ($result > 0) {
			$found = true;
		}
		if ($bypass) {
			return $found;
		}
		return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @param array $collection
	 * @param mixed $category
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function removeProgrammesFromCategory(array $collection, $category){
		$timeStamp = time();
		$response = $this->getTvProgrammeCategory($category);
		if ($response->error->exist) {
			return $response;
		}
		$category = $response->result->set;
		$idsToRemove = array();
		foreach ($collection as $p) {
			$response = $this->getTvProgramme($p);
			if ($response->error->exist) {
				continue;
			}
			$idsToRemove[] = $response->result->set->getId();
		}
		$in = ' IN (' . implode(',', $idsToRemove) . ')';
		$qStr = 'DELETE FROM ' . $this->entity['cotp']['name'] . ' ' . $this->entity['cotp']['alias']
			. ' WHERE ' . $this->entity['cotp']['alias'] . '.category ' . $category->getId()
			. ' WHERE ' . $this->entity['cotp']['alias'] . '.programme ' . $in;

		$q = $this->em->createQuery($qStr);
		$result = $q->getResult();

		$deleted = true;
		if (!$result) {
			$deleted = false;
		}
		if ($deleted) {
			return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
	}
	/**
	 * @param array $collection
	 * @param mixed $genre
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function removeProgrammesFromGenre(array $collection, $genre){
		$timeStamp = time();
		$response = $this->getTvProgrammeGenre($genre);
		if ($response->error->exist) {
			return $response;
		}
		$genre = $response->result->set;
		$idsToRemove = array();
		foreach ($collection as $p) {
			$response = $this->getTvProgramme($p);
			if ($response->error->exist) {
				continue;
			}
			$idsToRemove[] = $response->result->set->getId();
		}
		$in = ' IN (' . implode(',', $idsToRemove) . ')';
		$qStr = 'DELETE FROM ' . $this->entity['gotp']['name'] . ' ' . $this->entity['gotp']['alias']
			. ' WHERE ' . $this->entity['gotp']['alias'] . '.genre ' . $genre->getId()
			. ' WHERE ' . $this->entity['gotp']['alias'] . '.programme ' . $in;

		$q = $this->em->createQuery($qStr);
		$result = $q->getResult();

		$deleted = true;
		if (!$result) {
			$deleted = false;
		}
		if ($deleted) {
			return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
	}

	/**
	 * @param mixed $programme
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listGenresOfTvProgramme($programme, array $sortOrder = null, array $limit = null){
		$timeStamp = time();

		$response = $this->getTvProgramme($programme);
		if($response->error->exist){
			return $response;
		}
		/**
		 * @var BundleEntity\TvProgramme $programme
		 */
		$programme = $response->result->set;

		$qStr = 'SELECT ' . $this->entity['gotp']['alias']
			. ' FROM ' . $this->entity['gotp']['name'] . ' ' . $this->entity['gotp']['alias']
			. ' JOIN ' . $this->entity['gotp']['alias'] . '.programme ' . $this->entity['tvp']['alias']
			. ' WHERE ' . $this->entity['gotp']['alias'] . '.programme = ' . $programme->getId();


		$oStr = '';
		if ($sortOrder != null && count($sortOrder) > 0) {
			foreach ($sortOrder as $column => $direction) {
				$sorting = false;
				if (!in_array($column, array('name', 'url_key'))) {
					$sorting = true;
					switch ($column) {
						case 'id':
						case 'date_added':
						case 'date_updated':
						case 'date_removed':
						case 'date_removed':
						case 'title_original':
						case 'title_local':
						case 'production_year':
						case 'rating_tag':
						case 'presenter':
							$column = $this->entity['tvp']['alias'] . '.' . $column;
							break;
					}
					$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
				}
			}
			if ($sorting) {
				$oStr = rtrim($oStr, ', ');
				$oStr = ' ORDER BY ' . $oStr . ' ';
			}
		}
		$qStr .= $oStr;
		$query = $this->em->createQuery($qStr);
		$result = $query->getResult();
		$collection = [];
		foreach($result as $item){
			/**
			 * @var BundleEntity\GenresOfTvProgramme $item
			 */
			$collection[$item->getGenre()->getId()] = $item->getGenre();
		}
		$totalRows = count($collection);
		if($totalRows< 1){
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}

		return new ModelResponse($collection, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param mixed $programme
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listCategoriesOfTvProgramme($programme, array $sortOrder = null, array $limit = null){
		$timeStamp = time();

		$response = $this->getTvProgramme($programme);
		if($response->error->exist){
			return $response;
		}
		/**
		 * @var BundleEntity\TvProgramme $programme
		 */
		$programme = $response->result->set;

		$qStr = 'SELECT ' . $this->entity['cotp']['alias']
			. ' FROM ' . $this->entity['cotp']['name'] . ' ' . $this->entity['cotp']['alias']
			. ' JOIN ' . $this->entity['cotp']['alias'] . '.programme ' . $this->entity['tvp']['alias']
			. ' WHERE ' . $this->entity['cotp']['alias'] . '.programme = ' . $programme->getId();


		$oStr = '';
		if ($sortOrder != null && count($sortOrder) > 0) {
			foreach ($sortOrder as $column => $direction) {
				$sorting = false;
				if (!in_array($column, array('name', 'url_key'))) {
					$sorting = true;
					switch ($column) {
						case 'id':
						case 'date_added':
						case 'date_updated':
						case 'date_removed':
						case 'date_removed':
						case 'title_original':
						case 'title_local':
						case 'production_year':
						case 'rating_tag':
						case 'presenter':
							$column = $this->entity['tvp']['alias'] . '.' . $column;
							break;
					}
					$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
				}
			}
			if ($sorting) {
				$oStr = rtrim($oStr, ', ');
				$oStr = ' ORDER BY ' . $oStr . ' ';
			}
		}
		$qStr .= $oStr;
		$query = $this->em->createQuery($qStr);
		$result = $query->getResult();
		$collection = [];
		foreach($result as $item){
			/**
			 * @var BundleEntity\CategoriesOfTvProgramme $item
			 */
			$collection[$item->getCategory()->getId()] = $item->getCategory();
		}
		$totalRows = count($collection);
		if($totalRows< 1){
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}

		return new ModelResponse($collection, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @param mixed $tvProgrammeReminder
	 * @return ModelResponse
	 */
	public function getTvProgrammeReminder($tvProgrammeReminder) {
		$timeStamp = time();
		if($tvProgrammeReminder instanceof BundleEntity\TvProgrammeReminder){
			return new ModelResponse($tvProgrammeReminder, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
		}
		$result = null;
		switch($tvProgrammeReminder){
			case is_object($tvProgrammeReminder):
				$result = $this->em->getRepository($this->entity['tvpr']['name'])->findOneBy(array('member' => $tvProgrammeReminder->member, 'programme'=>$tvProgrammeReminder->programme));
				break;
		}
		if(is_null($result)){
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param array $collection
	 * @return array|ModelResponse
	 */
	public function insertTvProgrammeReminders(array $collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
		foreach($collection as $data){
			if($data instanceof BundleEntity\TvProgrammeReminder){
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if(is_object($data)){
				$entity = new BundleEntity\TvProgrammeReminder();
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
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
	 * @param array $collection
	 * @return array|ModelResponse
	 */
	public function deleteTvProgrammeReminders(array $collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\TvProgrammeReminder){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getTvProgrammeReminder($entry);
				if(!$response->error->exist){
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
	 * @param      $member
	 * @param null $sortOrder
	 * @param null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listRemindersOfMember($member, $sortOrder = null, $limit = null){
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvpr']['alias'] . '.member', 'comparison' => '=', 'value' => $member->getId()),
				)
			)
		);
		unset($response);
		return $this->listTvProgrammeReminders($filter, $sortOrder, $limit);
	}

	/**
	 * @param           $member
	 * @param \DateTime $date
	 * @param null      $sortOrder
	 * @param null      $limit
	 *
	 * @return mixed
	 */
	public function listFutureRemindersOfMember($member, \DateTime $date, $sortOrder = null, $limit = null){
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvpr']['alias'] . '.member', 'comparison' => '=', 'value' => $member->getId()),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['tvpr']['alias'] . '.date_reminder', 'comparison' => '>', 'value' => $date->format('Y-m-d H:i:s')),
				),
			)
		);
		unset($response);
		return $this->listTvProgrammeReminders($filter, $sortOrder, $limit);
	}

	/**
	 * @param null $filter
	 * @param null $sortOrder
	 * @param null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listTvProgrammeReminders($filter = null, $sortOrder = null, $limit = null) {
		$timeStamp = time();
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '.$this->entity['tvpr']['alias']
			.' FROM '.$this->entity['tvpr']['name'].' '.$this->entity['tvpr']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'date_reminder':
					case 'date_added':
					case 'date_updated':
					case 'date_removed':
						$column = $this->entity['tvpr']['alias'].'.'.$column;
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
}