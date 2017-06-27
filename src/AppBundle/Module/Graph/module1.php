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
    private $color;
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
        //$this->xTitle = 'Time';
        //$this->yTitle = 'Registration';
        //$this->interval = 24 * 3600 * 1000;
        $this->size = 300;

        $this->footer = "Total Users: " . $this->userRep->count('all');
        //$this->module->getModuleInfo()->setAvailableAnalytics(json_encode($this->availbeAnalytics));
        //dump($this->module->getModuleInfo()->getAvailableAnalytics());die();

    }

    /**
     * 'userType' => 'all', 'standard', 'internal'
     * 'keyword' => null, $keyword
     * 'analytics' => 'device-type', 'user-type', 'availability', 'device-type/user-type', 'availability/user-type', 'availability/device-type', 'mix'
     *
     * EX:
     * ['userType' => 'all', 'keyword' => null, 'analytics' => 'availability/device-type']
     *
     *
     * @param ArrayCollection $configuration
     * @return array
     */
    public function render(ArrayCollection $configuration)
    {
        $analytics = $configuration['analytics'];
        $type = $configuration['userType'];
        $keyword = $configuration['keyword'];

        if(  in_array($analytics, $this->module->getModuleInfo()->getAvailableAnalytics()) )
            call_user_func_array(array($this, $analytics), array($type, $keyword));
        else die('bad configuration');

        return get_object_vars($this);
    }

    public function default($type, $keyword){
        return $this->device_type($type, $keyword);
    }

    public function device_type($type, $keyword){
        $this->title = 'Device Type';

        $android = $this->userRep->countAccount($type, 'Android', $keyword);
        $ios = $this->userRep->countAccount($type, 'iPhone', $keyword) + $this->userRep->countAccount($type, 'iPad', $keyword);

        $this->data_name = 'Devices';
        $this->data = [
            [
                'y' => $android,
                'name' => 'Android'
            ],
            [
                'y' => $ios,
                'name' => 'ios'
            ]
        ];
        $this->color = ['#167dd8', '#76adde'];
    }

    public function user_type($type, $keyword){
        $this->title = 'User Type';

        $total = $this->userRep->count('all',  $keyword);
        $totalInter = $this->userRep->count('Internal', $keyword);

        $this->data_name = 'Users';
        $this->data = [
            [
                'y' => $total - $totalInter,
                'name' => 'External'
            ],
            [
                'y' => $totalInter,
                'name' => 'Internal'
            ]
        ];
        $this->color = ['#167dd8', '#76adde', '#fc5a2d', '#ff8b6b'];

    }

    public function availability($type, $keyword){
        $this->title = 'Availability';

        $total = $this->userRep->count($type,  $keyword);
        $totalORG= $this->userRep->countProfession($type, $keyword);

        $this->data_name = 'Users';
        $this->data = [
            [
                'y' => $total - $totalORG,
                'name' => 'Normal'
            ],
            [
                'y' => $totalORG,
                'name' => 'Orange Hat'
            ]
        ];
        $this->color = ['#167dd8', '#fc5a2d'];

    }

    public function device_type__user_type($type, $keyword){
        $this->title = 'Device Type / User Type';

        $android = $this->userRep->countAccount('all', 'Android', $keyword);
        $ios = $this->userRep->countAccount('all', 'iPhone', $keyword) + $this->userRep->countAccount($type, 'iPad', $keyword);
        $internalAndroid = $this->userRep->countAccount($type, 'Android', $keyword);
        $internalIos = $this->userRep->countAccount($type, 'iPhone', $keyword) + $this->userRep->countAccount($type, 'iPad', $keyword);

        $this->data_name = 'Users';
        $this->data = [
            [
                'y' => $android - $internalAndroid,
                'name' => 'Android'
            ],
            [
                'y' => $internalAndroid,
                'name' => 'Android Internal'
            ],
            [
                'y' => $ios - $internalIos,
                'name' => 'ios'
            ],
            [
                'y' => $internalIos,
                'name' => 'ios Internal'
            ]
        ];
        $this->color = ['#167dd8', '#76adde', '#fc5a2d', '#ff8b6b'];

    }

    public function availability__user_type($type, $keyword){
        $this->title = 'Availability / User Type';

        $total = $this->userRep->count('all',  $keyword);
        $totalInter = $this->userRep->count('Internal', $keyword);
        $totalORG = $this->userRep->countProfession('all', $keyword);
        $internalORG= $this->userRep->countProfession('Internal', $keyword);

        $this->data_name = 'Users';
        $this->data = [
            [
                'y' => $total - $totalORG - $totalInter + $internalORG,
                'name' => 'Normal'
            ],
            [
                'y' => $totalORG - $internalORG,
                'name' => 'External Orange Hat'
            ],
            [
                'y' => $internalORG,
                'name' => 'Internal Orange Hat'
            ],
            [
                'y' => $totalInter - $internalORG,
                'name' => 'Internal Non Orange Hat'
            ]
        ];
        $this->color = ['#167dd8', '#fc5a2d', '#ff8b6b', '#76adde'];

    }
    public function availability__device_type($type, $keyword){
        $this->title = 'Availability / Device Type';

        $android = $this->userRep->countAccount($type, 'Android', $keyword);
        $ios = $this->userRep->countAccount($type, 'iPhone', $keyword) + $this->userRep->countAccount($type, 'iPad', $keyword);
        $ORGAndroid = $this->userRep->countProfessionAccount($type, 'Android', $keyword);
        $ORGIos = $this->userRep->countProfessionAccount($type, 'iPhone', $keyword) + $this->userRep->countProfessionAccount($type, 'iPad', $keyword);

        $this->data_name = 'Users';
        $this->data = [
            [
                'y' => $android - $ORGAndroid,
                'name' => 'Normal Android'
            ],
            [
                'y' => $ios - $ORGIos,
                'name' => 'Normal ios'
            ],
            [
                'y' => $ORGAndroid,
                'name' => 'Orange Hat Android'
            ],
            [
                'y' => $ORGIos,
                'name' => 'Orange Hat ios'
            ]
        ];
        $this->color = ['#167dd8', '#76adde', '#fc5a2d', '#ff8b6b'];

    }
    public function mix($type, $keyword){
        $this->title = 'Availability / User Type / Device Type';

        $android = $this->userRep->countAccount('all', 'Android', $keyword);
        $ios = $this->userRep->countAccount('all', 'iPhone', $keyword) + $this->userRep->countAccount($type, 'iPad', $keyword);
        $internalAndroid = $this->userRep->countAccount('internal', 'Android', $keyword);
        $internalIos = $this->userRep->countAccount('internal', 'iPhone', $keyword) + $this->userRep->countAccount($type, 'iPad', $keyword);
        $internalORGAndroid = $this->userRep->countProfessionAccount('internal', 'Android', $keyword);
        $internalORGIos = $this->userRep->countProfessionAccount('internal', 'iPhone', $keyword) + $this->userRep->countProfessionAccount($type, 'iPad', $keyword);
        $totalORGAndroid = $this->userRep->countProfessionAccount('all', 'Android', $keyword);
        $totalORGIos = $this->userRep->countProfessionAccount('all', 'iPhone', $keyword) + $this->userRep->countProfessionAccount($type, 'iPad', $keyword);


        $this->data_name = 'Users';
        $this->data = [
            [
                'y' => $totalORGAndroid - $internalORGAndroid,
                'name' => 'Normal Orange Hat Android'
            ],
            [
                'y' => $totalORGIos - $internalORGIos,
                'name' => 'Normal Orange Hat ios'
            ],
            [
                'y' => ($android - $internalAndroid) - ($totalORGAndroid - $internalORGAndroid),
                'name' => 'Normal Non Orange Hat Android'
            ],
            [
                'y' => ($ios - $internalIos) - ($totalORGIos - $internalORGIos),
                'name' => 'Normal Non Orange Hat ios'
            ],
            [
                'y' => $internalORGAndroid,
                'name' => 'Internal Orange Hat Android'
            ],
            [
                'y' => $internalORGIos,
                'name' => 'Internal Orange Hat ios'
            ],
            [
                'y' => $internalAndroid - $internalORGAndroid,
                'name' => 'Internal Non Orange Hat Android'
            ],
            [
                'y' => $internalIos - $internalORGIos,
                'name' => 'Internal Non Orange Hat ios'
            ]
        ];
        $this->color = ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'];
    }

}