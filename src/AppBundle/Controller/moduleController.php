<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 10/06/17
 * Time: 11:46 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Module;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class moduleController extends Controller
{
    /**
     * @param $module Module
     * @Route("/module/{id}", name="render_module")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPageAction(Module $module){
        $moduleService = $this->get('app.module');
        $result = $moduleService->render($module);
        return $this->render('dashboard/module/graph/render.html.twig', [
            'result' => $result
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

        if($request->get('layout') != null){
            if( isset($request->get('layout')['info']) && $request->get('layout')['info'] != null){
                $moduleInfo =  $this->getDoctrine()->getRepository("AppBundle:ModuleInfo")->findOneBy(['id' => $request->get('layout')['info']]);
                $module->setModuleInfo($moduleInfo);
            }

            if( isset($request->get('layout')['rank']) && $request->get('layout')['rank'] != null ) $module->setRank($request->get('layout')['rank']);
            if( isset($request->get('layout')['size']) && $request->get('layout')['size'] != null ) $module->setSize($request->get('layout')['size']);
        }



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


        $em = $this->getDoctrine()->getManager();
        $em->persist($module);
        $em->flush();

        return new Response();
    }



}