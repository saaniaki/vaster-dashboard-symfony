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
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\DateRange;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\Configuration\Layout;
use AppBundle\Module\Configuration\Presentation;
use AppBundle\Module\Configuration\Search;
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

use GoogleBundle\Analytics\tst;


class dashboardController extends Controller
{
    //dashboard/users       ADMIN

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function showAction(){
/*        $configuration = new Configuration();
        $configuration->getCategories()->setSingle(['device_type']); // ['device_type', 'user_type', 'availability']

        $dateRanges = [];
        $range1 = new DateRange();

        $dateRanges[] = $range1;


        $configuration->getCategories()->setDate(['period' => $dateRanges]);

        dump($configuration->extract());die();
*/

        return $this->renderDashboard();

        /*$tst = new tst();


        dump($tst->printResults($tst->getReport()));die();

        return new Response($tst);*/
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

        return $this->render('dashboard/dashboard.html.twig', [
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
            /** @var $moduleToBeAdded Module*/
            $moduleToBeAdded = $form->getData();


            $lastModule = $page->getModules()->last();

            if( $moduleToBeAdded->getRank() === null ){
                if($lastModule != null)
                    $moduleToBeAdded->setRank($lastModule->getRank() + 100);
                else
                    $moduleToBeAdded->setRank(100);
            }

            $moduleToBeAdded->setPage($page);


            $configuration = new Configuration();
            $infoName = $moduleToBeAdded->getModuleInfo()->getName();
            if( $infoName == "Count" ) {
                $presentation = new Presentation();
                $presentation->setData('User Count');
                $configuration->setPresentation($presentation);
            }
            elseif ( $infoName == "Registration" ) {
                $presentation = new Presentation();
                $presentation->setData('Registration');
                $presentation->setInterval('Weekly');
                $configuration->setPresentation($presentation);
                $filters = new Filters();
                $date = new DateRange();
                //$date->setColumn('user.createdtime');
                //$date->setFrom(null);
                //$date->setTo(null);
                $filters->addDate('period', $date);
                $configuration->setFilters($filters);
            }

            $layout = new Layout();
            $layout->setSize($moduleToBeAdded->getPostedSize());
            $configuration->setLayout($layout);

            $moduleToBeAdded->setConfiguration($configuration->extract());



            $em = $this->getDoctrine()->getManager();
            $em->persist($moduleToBeAdded);
            $em->flush();
        }

        return $this->render('dashboard/module/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/edit-module/{id}", name="edit_module")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editModule(Request $request, Module $module){
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(NewModule::class, $module,  array(
            'entity_manager' => $em
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
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
     * @Route("/api/page/{id}/modules", name="page_modules")
     * @Method("POST")
     */
    public function renderPageAction(Page $page){
        $data = [ 'modules' => [] ];
        foreach ($page->getModules() as $module){
            //array_push($data['modules'], $module->getId());

            $data['modules'][] = [
                'id' => $module->getId(),
                'size' => $module->getSize()
            ];
        }

        return new JsonResponse($data);
    }

}