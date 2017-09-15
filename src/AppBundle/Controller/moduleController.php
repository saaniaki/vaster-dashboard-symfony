<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 10/06/17
 * Time: 11:46 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Module;
use AppBundle\Entity\Page;
use AppBundle\Form\NewModule;
use AppBundle\Module\Configuration\Categories;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User as AppUser;
use VasterBundle\Entity\User as VasterUser;

class moduleController extends Controller
{
    /**
     * @param $index integer
     * @param $section string
     * @Route("api/module/getsearch/{section}/{index}", name="get_module_search")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSearchFilter($section, $index){

        return $this->render('dashboard/module/dynamicFields/search.html.twig', [
            'section' => $section,
            'index' => $index,
            'searchColumns' => Search::$columns_available,
        ]);
    }

    /**
     * @param $section string
     * @param $index integer
     * @Route("api/module/getdate/{section}/{index}", name="get_module_date")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDateFilter($section, $index){

        return $this->render('dashboard/module/dynamicFields/date.html.twig', [
            'section' => $section,
            'index' => $index,
            'dateColumns' => DateRange::$columns_available
        ]);
    }

    /**
     * @param $module Module
     * @Route("/module/{id}", name="render_module")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPageAction(Module $module){
        $moduleService = $this->get('app.module');
        $result = $moduleService->render($module);

        // IMPORTANT $available conf
        $conf = $module->getModuleInfo()->getAvailableConfiguration();
        $presentations = $conf['presentation'];
        $userType = $conf['filters']['user_type'];
        $availabilities = $conf['filters']['availability'];
        $device_types = $conf['filters']['device_type'];


        return $this->render('dashboard/module/graph/render.html.twig', [
            'result' => $result,
            'presentations' => $presentations,
            'user_types' => $userType,
            'availabilities' => $availabilities,
            'device_types' => $device_types,
            'conf' => $module->getConfiguration(),
            'searchColumns' => Search::$columns_available,
            'dateColumns' => DateRange::$columns_available
        ]);
    }




    /**
     * accepts JSON and configures the module with module_id of {id}
     * @param $module Module
     * @param $request Request
     * @Route("api/module/{id}", name="set_module_conf")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method("POST")
     */
    public function setModuleConfAction(Request $request ,Module $module){
        /*
         * Getting the new json data by $request and parsing it to a Configuration object.
         * This will override any data that exists in the request and keeps the old parameters
         * if they are not provided in $request.
         */
        $data = new ArrayCollection();
        $data->set('categories', $request->get('categories'));
        $data->set('filters', $request->get('filters'));
        $data->set('layout', $request->get('layout'));
        $data->set('presentation', $request->get('presentation'));
        //$data->set('remove_zeros', $request->get('remove_zeros'));

        $configuration = $module->getConfiguration();           // To keep the old useful configuration

        $configuration->load($data);                            // To rewrite the new configuration
        $module->setConfiguration($configuration->extract());

        /*
         * These are basic information about each module, and they are not
         * stored in configuration. Therefore, they should be handled separately.
         */
        $info = $request->get('info');
        $rank = $request->get('rank');

        if($info != null) $module->setModuleInfo($this->getDoctrine()->getRepository("AppBundle:ModuleInfo")->findOneBy(['id' => $info]));
        if($rank != null) $module->setRank($rank);


        $em = $this->getDoctrine()->getManager();
        $em->persist($module);
        $em->flush();

        return new Response();
    }





    /**
     * @Route("api/module/add/start", name="module_add_start")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request){
        $version = $this->getParameter('version');
        /** @var $appUser AppUser */
        $appUser = $this->getUser();
        $vasterUser = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
            ->findOneBy([ 'email' => $appUser->getEmail()]);
        $pages = $appUser->getPages()->toArray();

        $em = $this->getDoctrine()->getManager();
        $modules_info = $em->getRepository("AppBundle:ModuleInfo")->findAll();

        $module_types =['Graph'];
        //$module_names = [];
        //foreach ($modules_info as $info) $module_names[] = $info->getName();

        $sizes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $colors = ['Green', 'Blue', 'Red'];


        return $this->render('dashboard/module/edit.html.twig', [
            "vasterUser" => $vasterUser,
            "version" => $version,
            'pages' => $pages,
            'module_types' => $module_types,
            'graph_types' => $modules_info,
            'module_sizes' => $sizes,
            'module_colors' => $colors
        ]);
    }

    /**
     * @Route("api/module/add/{moduleInfo_id}", name="module_add_tabs")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addTabs(Request $request, int $moduleInfo_id){
        $em = $this->getDoctrine()->getManager();
        $module_info = $em->getRepository("AppBundle:ModuleInfo")->findOneBy(["id" => $moduleInfo_id]);

        $conf = $module_info->getAvailableConfiguration();
        $presentations = $conf['presentation'];
        $userType = $conf['filters']['user_type'];
        $availabilities = $conf['filters']['availability'];
        $device_types = $conf['filters']['device_type'];

        return $this->render('dashboard/module/tabs.html.twig', [
            'presentations' => $presentations,
            'user_types' => $userType,
            'availabilities' => $availabilities,
            'device_types' => $device_types,
            'conf' => ['filters' => null, 'categories' => null]
        ]);
    }

    /**
     * @Route("api/module/tabs/{moduleInfo_id}/{id}", name="module_get_tabs",  defaults={"id" = null})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTabs(Request $request, int $moduleInfo_id, Module $module = null){
        $em = $this->getDoctrine()->getManager();
        $module_info = $em->getRepository("AppBundle:ModuleInfo")->findOneBy(["id" => $moduleInfo_id]);

        $conf = $module_info->getAvailableConfiguration();
        $presentations = $conf['presentation'];
        $userType = $conf['filters']['user_type'];
        $availabilities = $conf['filters']['availability'];
        $device_types = $conf['filters']['device_type'];

        $parameters = [
            'presentations' => $presentations,
            'user_types' => $userType,
            'availabilities' => $availabilities,
            'device_types' => $device_types,
            'conf' => ['filters' => null, 'categories' => null]
        ];


        if($module != null){
            $parameters['searchColumns'] = Search::$columns_available;
            $parameters['dateColumns'] = DateRange::$columns_available;
            $parameters['conf'] = $module->getConfiguration();
            $parameters['module'] = $module;
        }


        return $this->render('dashboard/module/tabs.html.twig', $parameters);
    }

    /**
     * @param $module Module
     * @Route("api/module/edit/{id}", name="module_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tstRender(Module $module){
        $version = $this->getParameter('version');
        /** @var $appUser AppUser */
        $appUser = $this->getUser();
        $vasterUser = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
            ->findOneBy([ 'email' => $appUser->getEmail()]);
        $pages = $appUser->getPages()->toArray();

        $em = $this->getDoctrine()->getManager();
        $modules_info = $em->getRepository("AppBundle:ModuleInfo")->findAll();

        $module_types =['Graph'];


        $sizes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $colors = ['Green', 'Blue', 'Red'];


        // IMPORTANT $available conf
        $conf = $module->getModuleInfo()->getAvailableConfiguration();
        $presentations = $conf['presentation'];
        $userType = $conf['filters']['user_type'];
        $availabilities = $conf['filters']['availability'];
        $device_types = $conf['filters']['device_type'];


        return $this->render('dashboard/module/edit.html.twig', [
            "vasterUser" => $vasterUser,
            "version" => $version,
            'pages' => $pages,
            'module_types' => $module_types,
            'graph_types' => $modules_info,
            'module_sizes' => $sizes,
            'module_colors' => $colors,


            'module' => $module,
            'presentations' => $presentations,
            'user_types' => $userType,
            'availabilities' => $availabilities,
            'device_types' => $device_types,
            'conf' => $module->getConfiguration(),
            'searchColumns' => Search::$columns_available,
            'dateColumns' => DateRange::$columns_available
        ]);
    }


    /**
     * @Route("api/module/add/{id}", name="module_add")
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
            if ( $infoName == "Bar Chart" ) {
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
            }elseif ( $infoName == "Pie Chart" ) {
                $presentation = new Presentation();
                $presentation->setData('Registration');
                $configuration->setPresentation($presentation);
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

    /*
     * @Route("api/module/edit/{id}", name="module_edit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
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
     * @Route("api/module/remove/{id}", name="module_remove")
     * @return \Symfony\Component\HttpFoundation\Response
     *
    public function removeModule(Module $module){

        // must check if the record belongs to this user

        $em = $this->getDoctrine()->getManager();
        $em->remove($module);
        $em->flush();

        return new Response("Module has been removed!");
    }
    */
}