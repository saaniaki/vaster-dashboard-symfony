<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 31/05/17
 * Time: 7:12 PM
 */

namespace AppBundle\Controller;


use AppBundle\AppBundle;
use AppBundle\Entity\Module;
use AppBundle\Entity\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use VasterBundle\VasterBundle;
use AppBundle\Entity\User as AppUser;
use VasterBundle\Entity\User as VasterUser;


class dashboardController extends Controller
{
    //dashboard/users       ADMIN

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function showAction(){
        $version = $this->getParameter('version');
        /** @var $appUser AppUser */
        $appUser = $this->getUser();

        /** @var $vasterUser VasterUser */
        $vasterUser = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
            ->findOneBy([ 'email' => $appUser->getEmail()]);


        $page = $appUser->getPages()[1];

        foreach ($page->getModules() as $module) {
            dump($module->getName());
        }


        /*
        $page = new Page();
        $page->setName("testPage4");
        $page->setRank(200);
        $page->setUser($appUser);


        $em = $this->getDoctrine()->getManager();
        $em->persist($page);
        $em->flush();
        */








        //dump($vasterUser);

        return $this->render('dashboard/show.html.twig', [
            "vasterUser" => $vasterUser,
            "version" => $version
        ]);
    }
}