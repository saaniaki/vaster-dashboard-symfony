<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 10/06/17
 * Time: 11:46 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Module;
use AppBundle\Module\Configuration\Categories;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\DateRange;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\Configuration\Search;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class moduleController extends Controller
{
    /**
     * @param $module Module
     * @param $index integer
     * @param $section string
     * @Route("/module/{id}/search/{section}/{index}", name="get_module_search_filter")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSearchFilter(Module $module, $section, $index){

        return $this->render('dashboard/module/graph/searchFilter.html.twig', [
            'module' => $module,
            'conf' => $module->getConfiguration(),
            'section' => $section,
            'index' => $index,
            'searchColumns' => Search::$columns_available,
            'dateColumns' => DateRange::$columns_available
        ]);
    }

    /**
     * @param $module Module
     * @param $section string
     * @param $index integer
     * @Route("/module/{id}/date/{section}/{index}", name="get_module_date_filter")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDateFilter(Module $module, $section, $index){

        return $this->render('dashboard/module/graph/dateFilter.html.twig', [
            'module' => $module,
            'conf' => $module->getConfiguration(),
            'section' => $section,
            'index' => $index,
            'searchColumns' => Search::$columns_available,
            'dateColumns' => DateRange::$columns_available
        ]);
    }

    /**
     * @param $module Module
     * @Route("/module/{id}", name="render_module")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPageAction(Module $module){
        $moduleService = $this->get('app.module');
        $result = $moduleService->render($module);

        $conf = $module->getModuleInfo()->getAvailableConfiguration();
        $presentations = $conf['presentation'];
        $userType = $conf['filters']['user_type'];
        $availabilities = $conf['filters']['availability'];
        $device_types = $conf['filters']['device_type'];


        return $this->render('dashboard/module/graph/render.html.twig', [
            'result' => $result,
            'presentations' => $presentations,
            'user_types' => $userType,
            'availabilities' => $availabilities,
            'device_types' => $device_types,
            'conf' => $module->getConfiguration(),
            'searchColumns' => Search::$columns_available,
            'dateColumns' => DateRange::$columns_available
        ]);
    }




    /**
     * accepts JSON and configures the module with module_id of {id}
     * @param $module Module
     * @param $request Request
     * @Route("api/module/{id}", name="set_module_conf")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("POST")
     */
    public function setModuleConfAction(Request $request ,Module $module){

        /*
         * Getting the new json data by $request and parsing it to a Configuration object.
         * This will override any data that exists in the request and keeps the old parameters
         * if they are not provided in $request.
         */
        $data = new ArrayCollection();
        $data->set('categories', $request->get('categories'));
        $data->set('filters', $request->get('filters'));
        $data->set('layout', $request->get('layout'));
        $data->set('presentation', $request->get('presentation'));
        $data->set('remove_zeros', $request->get('remove_zeros'));

        $configuration = $module->getConfiguration();           // To keep the old useful configuration
        $configuration->load($data);                            // To rewrite the new configuration
        $module->setConfiguration($configuration->extract());

        /*
         * These are basic information about each module, and they are not
         * stored in configuration. Therefore, they should be handled separately.
         */
        $info = $request->get('info');
        $rank = $request->get('rank');

        if($info != null) $module->setModuleInfo($this->getDoctrine()->getRepository("AppBundle:ModuleInfo")->findOneBy(['id' => $info]));
        if($rank != null) $module->setRank($rank);


/*

        if($request->get('settings') != null){

            if( isset($request->get('settings')['analytics']) && $request->get('settings')['analytics'] != null ) $module->setAnalytics($request->get('settings')['analytics']);
            if( isset($request->get('settings')['userType']) ) {
                if( $request->get('settings')['deviceType'] == null ) $module->setUserType(null);
                else $module->setUserType($request->get('settings')['userType']);
            }
            if( isset($request->get('settings')['deviceType']) ) {
                if( $request->get('settings')['deviceType'] == 'android' ) $module->setDeviceType('android');
                else if( $request->get('settings')['deviceType'] == 'ios' ) $module->setDeviceType('ios');
                else if( $request->get('settings')['deviceType'] == null ) $module->setDeviceType(null);
                else die("error");
            }
            if( isset($request->get('settings')['availability']) ) {
                if( $request->get('settings')['availability'] == 'true' ) $module->setAvailability(true);
                else if( $request->get('settings')['availability'] == 'false' ) $module->setAvailability(false);
                else if( $request->get('settings')['availability'] == null ) $module->setAvailability(null);
                else die("error");
            }
            if( isset($request->get('settings')['keyword']) ) $module->setKeyword($request->get('settings')['keyword']);


            $yesterday = new \DateTime('2000-01-01');
            $aWeekAgo = new \DateTime('2000-01-07');
            $aMonthAgo = new \DateTime('2000-02-01');


            if( isset($request->get('settings')['fromDate']) ) {
                $fromDate = $request->get('settings')['fromDate'];

                if ($fromDate == 'Yesterday') $module->setFromDate($yesterday);
                elseif ($fromDate == 'A week ago') $module->setFromDate($aWeekAgo);
                elseif ($fromDate == 'A month ago') $module->setFromDate($aMonthAgo);
                elseif ($fromDate != null) $module->setFromDate(new \DateTime($fromDate)); // need to check the value!!! all of the values must be checked
                else $module->setFromDate(null);
            };

            if( isset($request->get('settings')['toDate']) ) {
                $toDate = $request->get('settings')['toDate'];

                if ($toDate == 'Yesterday') $module->setToDate($yesterday);
                elseif ($toDate == 'A week ago') $module->setToDate($aWeekAgo);
                elseif ($toDate == 'A month ago') $module->setToDate($aMonthAgo);
                elseif ($toDate != null) $module->setToDate(new \DateTime($toDate));
                else $module->setToDate(null);
            };

        }
*/


        $em = $this->getDoctrine()->getManager();
        $em->persist($module);
        $em->flush();

        return new Response();
    }



}