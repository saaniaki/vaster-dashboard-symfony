<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/10/17
 * Time: 4:12 PM
 */

namespace AppBundle\Module\Configuration;


use AppBundle\Entity\FieldInfo;
use AppBundle\Service\vdpModule;

class Expression
{
    /** @var string : a string (char) to indicate this expression later in a "condition" */
    private $indicator;
    /** @var FieldInfo : an entity object which hold the information about this field */
    private $field;
    /** @var string : this is the operation that should be applied on the field and the value */
    private $operator;
    /** @var string : the value of the right side of this expression */
    private $value;

    public function __construct(string $indicator, string $sourceAlias, string $fieldAlias, string $operator, string $value)
    {
        $this->indicator = $indicator;
        $this->field = vdpModule::$fieldInfoService->validate($sourceAlias, $fieldAlias, false); //cannot apply anything on snapshots yet
        $this->operator = $operator;
        $this->value = $value;
    }

}