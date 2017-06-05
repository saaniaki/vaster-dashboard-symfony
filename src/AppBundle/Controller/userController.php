<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-05
 * Time: 12:49 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class userController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(UserRegistrationForm::class);


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /** @var $user User*/
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Welcome " . $user->getEmail());

            /*
            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
            */

            return $this->redirectToRoute('security_login');

        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}