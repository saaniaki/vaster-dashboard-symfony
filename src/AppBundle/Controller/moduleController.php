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
        return $this->render('dashboard/module/graph/module1.html.twig', [ //tst
            'result' => $result
        ]);
    }


    /**
     * @param $module Module
     * @Route("api/module/{id}", name="set_module_conf")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("POST")
     */
    public function setModuleConfAction(Request $request ,Module $module){

        $module->setAnalytics($request->get('analytics'));
        $module->setUserType($request->get('userType'));
        $module->setKeyword($request->get('keyword'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($module);
        $em->flush();

        return new Response($module->getModuleInfo()->getName() . ":" . $module->getId() . " saved");
    }



}