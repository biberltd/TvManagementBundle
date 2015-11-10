<?php
/**
 * @vendor      BiberLtd
 * @package		TvManagementBundle
 * @subpackage	Services
 * @name	    MaintenanceListener
 *
 * @author		Can Berkol
 *
 * @version     1.0.1
 * @date        22.06.2015
 *
 */

namespace BiberLtd\Bundle\TvManagementBundle\Listeners;

use BiberLtd\Bundle\CoreBundle\Core as Core;
use BiberLtd\Bundle\TvManagementBundle\Services as BundleServices;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MaintenanceListener extends Core{
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
     * @version         1.0.0
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
     * @version         1.0.0
     *
     * @param 			GetResponseEvent 	        $e
     *
     */
    public function onKernelRequest(GetResponseEvent $e){
        $request = $e->getRequest();

        $currentDomain = $request->getHttpHost();

        $this->session = $this->kernel->getContainer()->get('session');
        if(!$this->session->get('is_logged_in')){
            $response = $this->siteManagement->getSiteByDomain(str_replace('www.', '', $currentDomain));
            $routeName = $request->get('_route');
            if(!$response->error->exist){
                $settings = json_decode($response->result->set->getSettings());
                if(is_object($settings) && isset($settings->maintenance) && $settings->maintenance == true){
                    $url = $this->kernel->getContainer()->get('router')->generate($this->kernel->getContainer()->getParameter('maintenance_route'), array(), UrlGeneratorInterface::ABSOLUTE_PATH);
                    if($this->kernel->getContainer()->getParameter('maintenance_route') != $routeName){
                        $e->setResponse(new RedirectResponse($url));
                    }
                }
            }
            if($this->kernel->getContainer()->getParameter('maintenance') !== null && $this->kernel->getContainer()->getParameter('maintenance') === true){
                $url = $this->kernel->getContainer()->get('router')->generate($this->kernel->getContainer()->getParameter('maintenance_route'), array(), UrlGeneratorInterface::ABSOLUTE_PATH);
                if($this->kernel->getContainer()->getParameter('maintenance_route') != $routeName){
                    $e->setResponse(new RedirectResponse($url));
                }
            }
        }
    }
}
/**
 * Change Log
 * ****************************************
 * v1.0.1						 22.06.2015
 * Can Berkol
 * ****************************************
 * FR :: Maintenance mode now allows logged-in access.
 *
 * ****************************************
 * v1.0.0						 21.06.2015
 * Can Berkol
 * ****************************************
 * File is created.
 */