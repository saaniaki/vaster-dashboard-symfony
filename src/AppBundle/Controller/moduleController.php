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

        dump(Search::$columns_available);

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
     * accepts JSON
     * {layout: {info, rank, size}, settings: {analytics, userType, keyword, fromDate, toDate}}
     * @param $module Module
     * @Route("api/module/{id}", name="set_module_conf")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("POST")
     */
    public function setModuleConfAction(Request $request ,Module $module){

        $categories = $request->get('categories');
        $filters = $request->get('filters');
        $presentation = $request->get('presentation');
        $removeZeros = $request->get('remove_zeros');

        ////////////////////////////////////////////////////////////////////////// Filters: creating $filtersObj
        $filtersObj = new Filters();

        if( isset($filters['user_type']) && $filters['user_type'] != null )$filtersObj->setUserType($filters['user_type']);
        if( isset($filters['availability']) && $filters['availability'] != null )$filtersObj->setAvailability($filters['availability']);
        if( isset($filters['device_type']) && $filters['device_type'] != null )$filtersObj->setDeviceType($filters['device_type']);

        if( isset($filters['search']) ){
            foreach ( $filters['search'] as $name => $SearchArray ){
                $search = new Search();
                $search->setKeyword($SearchArray['keyword']);
                $search->setColumnOperator($SearchArray['columnOperator']);
                $search->setExpressionOperator($SearchArray['expressionOperator']);
                $search->setColumns($SearchArray['columns']);
                $search->setNegate($SearchArray['negate'] === 'true'? true: false);
                $filtersObj->addSearch($name, $search);
            }
        }

        if( isset($filters['date']) ){
            foreach ( $filters['date'] as $name => $rangeArray ){
                $range = new DateRange();
                $range->setFrom($rangeArray['from']);
                $range->setTo($rangeArray['to']);
                $range->setColumn($rangeArray['column']);
                $range->setOperator($rangeArray['operator']);
                $range->setNegate($rangeArray['negate'] === 'true'? true: false);
                $filtersObj->addDate($name, $range);
            }
        }

        ////////////////////////////////////////////////////////////////////////// Categories: creating $categoriesObj
        $categoriesObj = new Categories();
        if( isset($categories['single']) && $categories['single'] != null )$categoriesObj->setSingle($categories['single']);

        if( isset($categories['multi']['search']) ) {
            foreach ($categories['multi']['search'] as $name => $SearchArray) {
                $search = new Search();
                $search->setKeyword($SearchArray['keyword']);
                $search->setColumnOperator($SearchArray['columnOperator']);
                $search->setExpressionOperator($SearchArray['expressionOperator']);
                $search->setColumns($SearchArray['columns']);
                $search->setNegate($SearchArray['negate'] === 'true'? true: false);
                $categoriesObj->addSearch($name, $search);
            }
        }

        if( isset($categories['multi']['date']) ) {
            foreach ($categories['multi']['date'] as $name => $rangeArray) {
                $range = new DateRange();
                $range->setFrom($rangeArray['from']);
                $range->setTo($rangeArray['to']);
                $range->setColumn($rangeArray['column']);
                $range->setOperator($rangeArray['operator']);
                $range->setNegate($rangeArray['negate'] === 'true'? true: false);
                $categoriesObj->addDate($name, $range);
            }
        }

        ////////////////////////////////////////////////////////////////////////// Configuration: setting up $configuration
        $configuration = new Configuration();
        if( isset($removeZeros) && $removeZeros != null )$configuration->setRemoveZeros($removeZeros);
        if( isset($presentation) && $presentation != null )$configuration->setPresentation($presentation);
        $configuration->setFilters($filtersObj);
        $configuration->setCategories($categoriesObj);

        dump($configuration);

        ////////////////////////////////////////////////////////////////////////// Layout: a part of old code
        if($request->get('layout') != null){
            if( isset($request->get('layout')['info']) && $request->get('layout')['info'] != null){
                $moduleInfo =  $this->getDoctrine()->getRepository("AppBundle:ModuleInfo")->findOneBy(['id' => $request->get('layout')['info']]);
                $module->setModuleInfo($moduleInfo);
            }

            if( isset($request->get('layout')['rank']) && $request->get('layout')['rank'] != null ) $module->setRank($request->get('layout')['rank']);
            if( isset($request->get('layout')['size']) && $request->get('layout')['size'] != null ) $module->setSize($request->get('layout')['size']);
        }

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

        $module->setConfiguration($configuration->extract());

        $em = $this->getDoctrine()->getManager();
        $em->persist($module);
        $em->flush();

        return new Response();
    }



}