<?php
/**
 * @vendor      BiberLtd
 * @package		TvManagementBundle
 * @subpackage	Services
 * @name	    DomainListener
 *
 * @author		Can Berkol
 *
 * @version     1.0.5
 * @date        14.07.2015
 *
 */

namespace BiberLtd\Bundle\TvManagementBundle\Listeners;

use BiberLtd\Bundle\CoreBundle\Core as Core;
use BiberLtd\Bundle\TvManagementBundle\Services as BundleServices;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class DomainListener extends Core{
    /**
     * @name            __construct()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           BundleServices\SiteManagementModel      $siteManagement
     * @param           object       $kernel
     */
    public function __construct(BundleServices\SiteManagementModel $siteManagement, $kernel){
        parent::__construct($kernel);
        $this->siteManagement = $siteManagement;
        $this->kernel = $kernel;
    }
    /**
     * @name            __destruct()
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.3.0
     *
     */
    public function __destruct(){
        foreach($this as $property => $value) {
            $this->$property = null;
        }
    }
    /**
     * @name 			onKernelRequest()
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.5
     *
     * @param 			GetResponseEvent 	        $e
     *
     */
    public function onKernelRequest(\Symfony\Component\HttpKernel\Event\GetResponseEvent $e){
        $request = $e->getRequest();

        $currentDomain = $request->getHttpHost();

        $response = $this->siteManagement->getSiteByDomain(str_replace('www.', '', $currentDomain));

		if($response->error->exist){
			$this->kernel->getContainer()->get('session')->set('_currentSiteId', 1);
			return;
		}

		$site = $response->result->set;

        $this->kernel->getContainer()->get('session')->set('_currentSiteId', $site->getId());
        return;
    }
}
/**
 * Change Log
 * ****************************************
 * v1.0.5						 14.07.2015
 * Can Berkol
 * ****************************************
 * FR :: 3806788 :: The listener can now get the site by alias domains.
 *
 * ****************************************
 * v1.0.4						 18.06.2015
 * Can Berkol
 * ****************************************
 * BF :: Now strips www.
 *
 * ****************************************
 * v1.0.3						25.05.2015
 * Can Berkol
 * ****************************************
 * BF :: Typo fixed ($this->error->exists to $this->error->exist)
 *
 * ****************************************
 * v1.0.2						01.05.2015
 * Can Berkol
 * ****************************************
 * CR :: Changes made to be compatible with CoreBundle v3.3
 *
 * ****************************************
 * v1.0.1						30.04.2015
 * Can Berkol
 * ****************************************
 * BF :: Namespace fixed.
 * BF :: onKernelRequest :: The code wasn't returning on error.
 *
 * ****************************************
 * v1.0.0						27.04.2015
 * TW #
 * Can Berkol
 * ****************************************
 * A __construct()
 * A onKernelRequest()
 */