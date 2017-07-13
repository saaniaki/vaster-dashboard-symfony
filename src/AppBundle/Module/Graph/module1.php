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

class module1 implements ModuleInterface
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
    private $data = [];
    private $data_name;

    private $footer;


    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        $this->module = $module;
        $em = $managerRegistry->getManager('vaster');
        $this->userRep = $em->getRepository("VasterBundle:User");
        $this->type = 'pie';
        $this->size = 200;
        //$this->footer = "Total Users: " . $this->userRep->generalCount();

    }

    /**
     * @param ArrayCollection $configuration
     * @return array
     */
    public function render(ArrayCollection $configuration)
    {
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
            $categories[$cat] = $this->module->getModuleInfo()->getAvailableConfiguration()['filters'][$cat];//get actual values from module info
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



        //dump($this->combinations($categories));
        $this->makeNames($this->combinations($categories), $filters, $removeZeros);
        //die();

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

    private function makeNames($combinations, $filters, $removeZeros){

        foreach( $combinations as $combo){
            $name = $userType = $deviceType = $availability = $searches = $dates = null;

            foreach( $combo as $key => $cat ){
                if( is_string($cat) ) {
                    // $key is the type
                    $name .= "/" . $cat;

                    if( strtolower($key) == 'usertype' ) $userType = [$cat];

                    if( strtolower($key) == 'availability' ){
                        if( strtolower($cat) == "orange hat" ) $availability = true;
                        else $availability = false;
                    }
                    if( strtolower($key) == 'devicetype' ){
                        if( strtolower($cat) == "android" ) $deviceType = ["android"];
                        else if( strtolower($cat) == "ios" ) $deviceType = ["iPhone", "iPad"];
                    }

                }
                else {
                    if( strtolower($cat['type']) == 'search' ) $searches = $cat['value'];

                    if( strtolower($cat['type']) == 'date' ) $dates = $cat['value'];

                    if( $cat['match'] ) $name .= "/" . $key;
                    else {
                        $name .= "/!" . $key;
                        if( strtolower($cat['type']) == 'search' ) foreach ( $searches as $k => $search ) $searches[$k]['negate'] = !$search['negate'];
                        if( strtolower($cat['type']) == 'date' ) foreach ( $dates as $k => $date ) $dates[$k]['negate'] = !$date['negate'];
                    }
                }
            }

            $name = substr($name, 1);

            if( $name == null ) $name .= "All Users : " . $this->userRep->generalCount($userType, $availability, $deviceType, $searches, $dates);
            else {

                $query = $this->userRep->createQueryBuilder('user')->select('COUNT(user)');

                //$name .= " : " . $this->userRep->generalCount($userType, $availability, $deviceType, $searches, $dates);

                $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
                $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
                $query = $this->userRep->applyFilter($filters, $query);
                $query = $this->userRep->NEWmodifyCount($userType, $availability, $deviceType, $searches, $dates, $query);
                $number = $query->getQuery()->getSingleScalarResult();
                //dump($query->getQuery());
                //$number = 0;
                //$name .= " : " . $number;

                if( !$removeZeros || $number != 0 ){
                    $this->data_name = 'Users';
                    $this->data[] = [
                        'y' => $number,
                        'name' => $name
                    ];
                }

                //dump($this->data);

            }

        }

        return $combinations;
    }


}




































