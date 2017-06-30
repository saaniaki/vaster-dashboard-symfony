<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-13
 * Time: 10:41 AM
 */

namespace AppBundle\Controller;
use Doctrine\DBAL\Types\Type;
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
use VasterBundle\Entity\Account;
use VasterBundle\Entity\LastSeen;
use VasterBundle\Entity\Location;
use VasterBundle\Entity\Profession;
use VasterBundle\Entity\User;
use VasterBundle\Form\UserUpdate;
use VasterBundle\VasterBundle;
use AppBundle\Entity\User as AppUser;
use VasterBundle\Entity\User as VasterUser;


class adminController extends Controller
{
    /**
     * @Route("/admin/users", name="user_management")
     */
    public function showAction(){
        $version = $this->getParameter('version');
        /** @var $appUser AppUser */
        $appUser = $this->getUser();
        /** @var $vasterUser VasterUser */
        $vasterUser = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster")
            ->findOneBy([ 'email' => $appUser->getEmail()]);
        $pages = $appUser->getPages()->toArray();
        $pages = $appUser->getPages()->toArray();

        return $this->render('admin/users.html.twig', [
            'vasterUser' => $vasterUser,
            'version' => $version,
            'pages' => $pages
        ]);
    }


    /**
     * @Route("/admin/api/user/count/{type}/{keyword}", name="api_user_count")
     * /admin/api/user/page/20/20/user.email/ASC
     */
    public function userCountAction($type, $keyword = null){

        $userRep = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster");

        $total = $userRep->count($type, $keyword);
        $totalInter = $userRep->count('Internal', $keyword);
        $totalORG= $userRep->countProfession($type, $keyword);
        $android = $userRep->countAccount($type, 'Android', $keyword);
        $ios = $userRep->countAccount($type, 'iPhone', $keyword) + $userRep->countAccount($type, 'iPad', $keyword);

        $result[] = [
            'total' => $total,
            'totalInter' => $totalInter,
            'totalORG' => $totalORG,
            'android' =>$android,
            'ios' => $ios,
        ];

        //dump($result);die();
        $data = [
            'count' => $result
        ];
        return new JsonResponse($data);
    }


    /**
     * @Route("/admin/api/user/page/{limit}/{offset}/{sort}/{order}/{internal}/{keyword}", name="api_user_page")
     * /admin/api/user/page/20/20/user.email/ASC
     */
    public function userPageAction($limit, $offset, $sort, $order, bool $internal, $keyword = null){

        $userRep = $this->getDoctrine()->getRepository("VasterBundle:User", "vaster");
        $locRep = $this->getDoctrine()->getRepository("VasterBundle:Location", "vaster");
        $searchRep = $this->getDoctrine()->getRepository("VasterBundle:Search", "vaster");


        if($internal){
            if( $keyword != null){
                $users = $userRep->showPageSearch($limit, $offset, $sort, $order, 'all',$keyword);
            }else{
                $users = $userRep->showPage($limit, $offset, $sort, $order);
            }
        } else {
            if( $keyword != null){
                $users = $userRep->showPageSearchExclude($limit, $offset, $sort, $order, 'internal',$keyword); // external
            }else{
                $users = $userRep->showPageExclude($limit, $offset, $sort, $order, 'internal');
            }
        }


        $result = [];
        foreach ($users as $user) {
            /** @var $profession Profession */
            $profession = $user->getProfession();
            /** @var $account Account */
            $account = $user->getAccount();
            $device = '';
            if($account != null){
                $device = $account->getDeviceType();
            }

            $location = $locRep->findValidLocation($user);
            $locHistory = $locRep->findHistory($user);

            /** @var $lastSeen LastSeen */
            $lastSeen = $user->getLastseen();
            if($lastSeen != null)
                $lastSeen = $lastSeen->getSeconds();
            else
                $lastSeen = '';



            $languages = $user->getLanguages();
            if( $languages!= null ){
                $languages = [
                    'first' => $languages->getFirstLanguage(),
                    'second' => $languages->getSecondLanguage(),
                    'other' => $languages->getOtherLanguage()
                ];
            }

            $socialNetworks = [];
            foreach ( $user->getSocialNetwork() as $sn ){
                $socialNetworks[] = [
                    'type' => $sn->getType(),
                    'url' => $sn->getUrl(),
                    'name' => $sn->getName()
                ];
            }

            $serviceTimes = [];
            foreach ( $user->getServiceTime() as $st ){
                $serviceTimes[] = [
                    'day' => $st->getWeekdays(),
                    'start' => $st->getStarttime(),
                    'end' => $st->getEndtime(),
                    'availability' => $st->getAvailability()
                ];
            }

            $searches = $searchRep->findHistory($user);

            $result[] = [ //just send the user!
                'id' => $user->getUserId(),
                'firstName' => $user->getFirstname(),
                'lastName' => $user->getLastname(),
                'phone' => $user->getPhone(),
                'email' => $user->getEmail(),
                'balance' => $user->getBalance(),
                'profile' => $user->getUrlprofile(),
                'cover' => $user->getUrlcover(),
                'type' => $user->getAccounttype(),
                'createdTime' => $user->getCreatedtime(),
                'profession' => [
                    'available' => $profession->getAvailable(),
                    'name' => $profession->getProfessionname(),
                    'title' => $profession->getTitle(),
                    'rate' => $profession->getServiceRate(),
                    'about' => $profession->getAbout(),
                    'expertise' => $profession->getExpertise(),
                    'commission' => $profession->getCommission(),
                    'gender' => $profession->getGender(),
                    'ranking' => $profession->getRanking(),
                    'votes' => $profession->getVotes(),
                    'address' => $profession->getAddress(),
                    'website' => $profession->getWebsite(),
                    'city' => $profession->getCity(),
                    'region' => $profession->getRegion(),
                    'postalCode' => $profession->getPostalCode(),
                    'country' => $profession->getCountry(),
                    'homeLocation' => $profession->getHomelocation()
                ],
                'socialNetworks' => $socialNetworks,
                'serviceTime' => $serviceTimes,
                'languages' => $languages,
                'device' => $device,
                'location' => $location,
                'locHistory' => $locHistory,
                'lastSeen' => $lastSeen,
                'searches' => $searches
            ];
        }
        //dump($result);die();
        $data = [
            'users' => $result
        ];
        return new JsonResponse($data);
        //return new Response('' . print_r($data));
    }

    /**
     * @Route("/admin/users/update/{id}", name="user_update")
     */
    public function userUpdateAction(Request $request, User $user){
        $form = $this->createForm(UserUpdate::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var $user User*/
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager("vaster");
            $em->persist($user);
            $em->flush();
        }

        return $this->render('admin/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}