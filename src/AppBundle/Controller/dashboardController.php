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

class dashboardController extends Controller
{
    /**
     * @Route("/dashboard/{username}")
     */
    public function shoAction($username){
        $version = $this->getParameter('version');

        return $this->render('dashboard/show.html.twig', [
            "username" => $username,
            "version" => $version
        ]);
    }
}