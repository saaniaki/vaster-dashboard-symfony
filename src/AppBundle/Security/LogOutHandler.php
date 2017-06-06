<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-06
 * Time: 10:03 AM
 */

namespace AppBundle\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogOutHandler implements LogoutHandlerInterface
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->session->getFlashBag()->add("success_logout", "You have successfully logged out.");
    }
}