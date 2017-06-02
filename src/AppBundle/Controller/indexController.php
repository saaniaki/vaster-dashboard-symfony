<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 01/06/17
 * Time: 9:39 PM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class indexController extends Controller
{
    /**
     * @Route("/")
     */
    public function showAction(){
        $version = $this->getParameter('version');

        return $this->render("index.html.twig", [
            "version" => $version
        ]);
    }
}