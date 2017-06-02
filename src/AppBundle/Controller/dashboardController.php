<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 31/05/17
 * Time: 7:12 PM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class dashboardController extends Controller
{
    /**
     * @Route("/dashboard/users")
     */
    public function listAction(){
        $em = $this->getDoctrine()->getManager();
        $userInfos = $em->getRepository('AppBundle:UserInfo')->findAll();
        dump($userInfos);die();
    }

    /**
     * @Route("/dashboard/newuser")
     */
    public function newAction(){
        //$userInfo = new UserInfo();
        //$userInfo->setId(1);
        //$userInfo->setUsername("saaniaki");
        //$userInfo->setPassword("password2");



        $em = $this->getDoctrine()->getManager();
        //$userInfo = $em->getRepository('AppBundle:UserInfo')->find(1);
        $userInfo = $em->getRepository('AppBundle:UserInfo')->findOneBy(['username' => 'saaniaki6']);



        if (!$userInfo) {
            throw $this->createNotFoundException('user not found');
        }
        $userInfo->setPassword("password10");


        $em->persist($userInfo);
        $em->flush();

        return new Response("User Created!");

    }

    /**
     * @Route("/dashboard/{username}")
     */
    public function showAction($username){
        $version = $this->getParameter('version');

        return $this->render('dashboard/show.html.twig', [
            "username" => $username,
            "version" => $version
        ]);
    }
}