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
use AppBundle\Form\NewPage;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        $noPage = false;
        $page = $appUser->getPages()->first();
        if($page == null){
            $noPage = true;
        }


        dump($noPage);

        return $this->render('dashboard/show.html.twig', [
            "vasterUser" => $vasterUser,
            "version" => $version,
            'page' => $appUser->getPages()->first(),
            'noPage' => $noPage
        ]);
    }

    /**
     * @Route("/dashboard/pages", name="manage_pages")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function managePages(Request $request){
        $version = $this->getParameter('version');
        /** @var $appUser AppUser */
        $appUser = $this->getUser();
        /** @var $vasterUser VasterUser */
        $vasterUser = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
            ->findOneBy([ 'email' => $appUser->getEmail()]);


        $pages = [];
        $lastPage = new Page();
        foreach ($appUser->getPages() as $page) {
            $pages[] = [
                'id' => $page->getID(),
                'name' => $page->getName(),
                'rank' => $page->getRank()
            ];
            $lastPage = $page;
        }


        /*$form = $this->createForm(NewPage::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var $newPage Page*
            $newPage = $form->getData();

            if( $newPage->getRank() == null ){
                $newPage->setRank($lastPage->getRank() + 50);
            }

            $newPage->setUser($appUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($newPage);
            $em->flush();

            dump($newPage);
        }*/

        return $this->render('dashboard/pages.html.twig', [
            "vasterUser" => $vasterUser,
            //'form' => $form->createView(),
            'version' => $version,
            'pages' => $pages
        ]);
    }


    /**
     * @Route("/dashboard/api/pages", name="api_pages")
     * @Method("GET")
     */
    public function getNotesAction()//change name
    {
        /** @var $appUser AppUser */
        $appUser = $this->getUser();
        $pages = [];
        //$lastPage = new Page();
        foreach ($appUser->getPages() as $page) {
            $pages[] = [
                'id' => $page->getID(),
                'name' => $page->getName(),
                'rank' => $page->getRank()
            ];
            //$lastPage = $page;
        }

        $data = [
            'pages' => $pages
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/dashboard/{id}/edit", name="edit_page")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editPage(Request $request, Page $page){
        $form = $this->createForm(NewPage::class, $page);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var $newPage Page*/
            $edittedPage = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($edittedPage);
            $em->flush();
        }

        return $this->render('dashboard/edit.html.twig', [
            'form' => $form->createView(),
            'page' => $page
        ]);
    }

    /**
     * @Route("/dashboard/{id}/remove", name="remove_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removePage(Page $page){

        // must check if the record belongs to this user

            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();

        return new Response("Page has been removed!");
    }

    /**
     * @Route("/dashboard/new", name="new_page")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newPage(Request $request){
        $form = $this->createForm(NewPage::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var $newPage Page*/
            $newPage = $form->getData();

            /** @var $appUser AppUser */
            $appUser = $this->getUser();

            $lastPage = $appUser->getPages()->last();

            if( $newPage->getRank() === null ){
                if($lastPage != null)
                    $newPage->setRank($lastPage->getRank() + 100);
                else
                    $newPage->setRank(100);
            }

            $newPage->setUser($appUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($newPage);
            $em->flush();
        }

        return $this->render('dashboard/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}