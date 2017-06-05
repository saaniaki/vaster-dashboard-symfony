<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-02
 * Time: 4:56 PM
 */

namespace AppBundle\Controller;


use AppBundle\Form\LoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class securityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction(){
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('dashboard');
        }


        $version = $this->getParameter('version');

        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginForm::class, [
            "_username" => $lastUsername
        ]);

        return $this->render('security/login.html.twig',[
            "form" => $form->createView(),
            "error" => $error,
            "version" => $version
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction(){
        throw new \Exception('this should not be reached!');
    }

}