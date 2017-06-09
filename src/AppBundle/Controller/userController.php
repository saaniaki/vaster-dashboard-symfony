<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-05
 * Time: 12:49 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Page;
use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VasterBundle\VasterBundle;

class userController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $version = $this->getParameter('version');
        $form = $this->createForm(UserRegistrationForm::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /** @var $user User*/
            $user = $form->getData();

            /** @var $possibleUsers ArrayCollection */
            $possibleUsers = new ArrayCollection($this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
                ->findBy(['email' => $user->getEmail()]));

            if( $possibleUsers->count() == 0 ){
                $this->get('session')->getFlashBag()->add("register_error", "This email does not exists!");
                return $this->redirectToRoute('user_register');
            } else if( $possibleUsers->count() == 1 ){

                if(!$this->pushAndMatch($possibleUsers, $user)){
                    $this->get('session')->getFlashBag()->add("register_error", "This user is not an internal user!");
                    return $this->redirectToRoute('user_register');
                }

            } else if ( $user->getPhone() == null ) {
                $this->get('session')->getFlashBag()->add("register_error", "This email is not unique!");
                return $this->redirectToRoute('user_register');
            } else if ( $user->getPhone() != null ) {
                $possibleUsers = new ArrayCollection($this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
                    ->findBy(['email' => $user->getEmail(), 'phone' => $user->getPhone()]));
                if( $possibleUsers->count() == 0){
                    $this->get('session')->getFlashBag()->add("register_error", "The email and phone number do not match!");
                    return $this->redirectToRoute('user_register');
                }

                if(!$this->pushAndMatch($possibleUsers, $user)) {
                    $this->get('session')->getFlashBag()->add("register_error", "This user is not an internal user!");
                    return $this->redirectToRoute('user_register');
                }
            }


            $newPage = new Page();
            $newPage->setRank(100);
            $newPage->setName("Default Page");
            $newPage->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($newPage);
            $em->flush();

            /*
            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
            */

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );


            //return $this->redirectToRoute('security_login');

        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'version' => $version
        ]);
    }

    /**
     * @param $possibleUsers ArrayCollection
     * @param $user User
     * returns true if user registered successfully
     * @return boolean
     */
    public function pushAndMatch($possibleUsers, $user)
    {
        $vasterUser = $possibleUsers->get(0);
        $user->setId($vasterUser->getUserId());
        if($vasterUser->getAccounttype() == "Internal"){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return true;
        }
        return false;

    }
}