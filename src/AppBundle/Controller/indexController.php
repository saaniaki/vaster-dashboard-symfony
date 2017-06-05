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
     * @Route("/", name="homepage")
     */
    public function showAction(){
        //add branding
        //add success logout
        return $this->redirectToRoute('security_login');
    }
}