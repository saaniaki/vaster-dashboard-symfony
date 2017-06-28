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
use AppBundle\Entity\ModuleInfo;
use AppBundle\Entity\Page;
use AppBundle\Form\NewModule;
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
        return $this->renderDashboard();
    }

    /**
     * @param $curPage Page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function renderDashboard(Page $curPage = null){
        $version = $this->getParameter('version');
        /** @var $appUser AppUser */
        $appUser = $this->getUser();

        /** @var $vasterUser VasterUser */
        $vasterUser = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
            ->findOneBy([ 'email' => $appUser->getEmail()]);


        $pages = $appUser->getPages()->toArray();
        //dump($appUser->getPages());die();
        if($pages == null){
            $this->get('session')->getFlashBag()->add("render_error", "You have no page to render, please add a page.");
            return $this->redirectToRoute('manage_pages');
        }

        if( $curPage == null )
            $curPage = $pages[0];
        //dump($curPage);die();

        return $this->render('dashboard/show.html.twig', [
            "vasterUser" => $vasterUser,
            "version" => $version,
            'pages' => $pages,
            'currentPage' => $curPage
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
        foreach ($appUser->getPages() as $page) {
            $pages[] = [
                'id' => $page->getID(),
                'name' => $page->getName(),
                'rank' => $page->getRank()
            ];
        }

        return $this->render('dashboard/pages.html.twig', [
            "vasterUser" => $vasterUser,
            //'form' => $form->createView(),
            'version' => $version,
            'pages' => $pages
        ]);
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


        $modules = $page->getModules();
        if($modules->count() == 0)
            $modules = null;



        if($form->isSubmitted() && $form->isValid()){
            /** @var $newPage Page*/
            $edittedPage = $form->getData();



            $em = $this->getDoctrine()->getManager();
            $em->persist($edittedPage);
            $em->flush();
        }

        return $this->render('dashboard/edit.html.twig', [
            'form' => $form->createView(),
            'page' => $page,

            'modules' => $modules
        ]);
    }

    /**
     * @Route("/dashboard/{id}/remove", name="remove_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removePage(Page $page){


        /** @var $appUser AppUser */
        $appUser = $this->getUser();

        $userPages = $appUser->getPages();

        // must check if the record belongs to this user
        // in future permissions should play role here
        if( $userPages->count() == 1 ){
            return new Response("This page can not be removed!");
        }

        $em = $this->getDoctrine()->getManager();

        $page->getModules();
        foreach ( $page->getModules() as $module ){
            $em->remove($module);
        }

        $em->remove($page);
        $em->flush();

        return new Response("Page has been removed!");
    }

    /**
     * @Route("/dashboard/{id}/remove-module", name="remove_module")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeModule(Module $module){

        // must check if the record belongs to this user

        $em = $this->getDoctrine()->getManager();
        $em->remove($module);
        $em->flush();

        return new Response("Module has been removed!");
    }

    /**
     * @Route("/dashboard/{id}/new-module", name="new_module")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newModule(Request $request, Page $page){
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(NewModule::class, null,  array(
            'entity_manager' => $em
            //'page' => $page
        ));


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var $newPage Module*/
            $moduleToBeAdded = $form->getData();


            $lastModule = $page->getModules()->last();

            if( $moduleToBeAdded->getRank() === null ){
                if($lastModule != null)
                    $moduleToBeAdded->setRank($lastModule->getRank() + 100);
                else
                    $moduleToBeAdded->setRank(100);
            }

            $moduleToBeAdded->setPage($page);

            //dump($moduleToBeAdded);die();

            //$moduleToBeAdded->setModuleInfo($allInfo[0]);


            $em = $this->getDoctrine()->getManager();
            $em->persist($moduleToBeAdded);
            $em->flush();
        }

        return $this->render('dashboard/module/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/dashboard/{id}", name="dashboard_alter")
     */
    public function showPageAction(Page $page){
        return $this->renderDashboard($page);
    }

    /**
     * @Route("/dashboard/render/{id}", name="render_page")
     */
    public function renderPageAction(Page $page){
        $version = $this->getParameter('version');
        /** @var $appUser AppUser */
        $appUser = $this->getUser();

        /** @var $vasterUser VasterUser */
        $vasterUser = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
            ->findOneBy([ 'email' => $appUser->getEmail()]);


        $modules = [];
        foreach ($page->getModules() as $module) {
            array_push($modules, $module);
        }


        return $this->render('dashboard/showPage.html.twig', [
            "vasterUser" => $vasterUser,
            "version" => $version,
            'page' => $page,
            'modules' => $modules
        ]);
    }





}