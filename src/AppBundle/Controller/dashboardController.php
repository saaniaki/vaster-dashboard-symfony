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
    //dashboard/users       ADMIN

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function showAction(){
        $version = $this->getParameter('version');

        return $this->render('dashboard/show.html.twig', [
            "username" => "saaniaki",
            "version" => $version
        ]);
    }
}