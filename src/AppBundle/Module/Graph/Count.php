<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 12/06/17
 * Time: 3:19 PM
 */

namespace AppBundle\Module\Graph;


use AppBundle\Entity\Module;
use AppBundle\Module\ModuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;

class Count implements ModuleInterface
{
    /**
     * @var Module
     */
    private $module;
    private $userRep;

    private $title;
    private $type;
    private $size;
    private $color = ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'];
    private $xTitle;
    private $yTitle;
    private $xValues;
    private $yValues;
    private $interval;

    private $footer;

    private $currentlyShowing = 0;

    private $tooltip_shared = 1; //true
    private $all_data = [];


    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        $this->module = $module;
        $em = $managerRegistry->getManager('vaster');
        $this->userRep = $em->getRepository("VasterBundle:User");
        $this->type = 'pie';
        $this->size = 200;
        $this->tooltip_shared = 0; // false but false is null so 0
    }

    /**
     * @param ArrayCollection $configuration
     * @return array
     */
    public function render(ArrayCollection $configuration)
    {
        //dump($configuration);die();

        $presentation = $configuration['presentation']; //this value should be parsed!!

        $filters = $configuration['filters'];
        $removeZeros = $configuration['remove_zeros'];

        /*
         * getting all the possible categories
         */
        $categories = [];
        $singleCategories = $configuration['categories']['single'];
        $multiCategories = new ArrayCollection($configuration['categories']['multi']);

        foreach ($singleCategories as $cat){
            $categories[strtolower($cat)] = $this->module->getModuleInfo()->getAvailableConfiguration()['filters'][strtolower($cat)];//get actual values from module info
        }


        foreach ($multiCategories as $type => $cat){
            foreach ($cat as $catName => $value) {
                $categories[$catName] = [
                    '0' => ['match' => false, 'value' => $value, 'type' => $type],
                    '1' => ['match' => true, 'value' => $value, 'type' => $type]
                ];
            }
        }



        /*if(  in_array($analytics, $this->module->getModuleInfo()->getAvailableAnalytics()) )
            call_user_func_array([$this, $analytics], [$userType, $keyword, $deviceType, $availability]);
        else die('bad configuration');*/




        $this->makeNames($presentation, $this->combinations($categories), $filters, $removeZeros);
        $this->title = 'Users Count';
        $totalUsers = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')->getQuery()->getSingleScalarResult();
        $this->footer = "Total Users: " . $totalUsers . " / Currently showing: " . $this->currentlyShowing;

        return get_object_vars($this);
    }

    private function combinations($data){
        $combinations = [[]];
        $comKeys = array_keys($data);

        for ($count = 0; $count < count($comKeys); $count++) {
            $tmp = [];
            foreach ($combinations as $v1) {
                foreach ($data[$comKeys[$count]] as $v2)
                    $tmp[] = $v1 + [$comKeys[$count] => $v2];

            }
            $combinations = $tmp;
        }

        return $combinations;
    }

    private function makeNames($presentation, $combinations, $filters, $removeZeros){
        $data = [];
        foreach( $combinations as $combo){
            $query = null;
            if( $presentation == 'User Count' ){
                $query = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')
                    ->orderBy('user.createdtime', 'DESC');
            } else {
                $query = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')
                    ->orderBy('user.createdtime', 'DESC');
            }


            $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query = $this->userRep->applyFilters($filters, $query);
            $catArray = $this->userRep->applyCategories($combo, $query);

            $name = $catArray['name'];
            if( $name == null ) $name .= "All Users";
            /** @var $query QueryBuilder */
            $query = $catArray['query'];

            $number = $query->getQuery()->getSingleScalarResult();
            //process
            if( !$removeZeros || $number != 0 ){
                //$this->data_name = 'Users';
                $data[] = [
                    'y' => $number,
                    'name' => $name
                ];
                $this->currentlyShowing += $number;
            }

        }
        //process
        $this->all_data[] = [
            'name' => 'Users',
            'data' => $data,
            'type' => 'pie'
        ];

        return $combinations;
    }


}




































