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

    public function __construct(string $indicator, string $sourceAlias, string $fieldAlias, bool $snapShot = false, string $operator, string $value)
    {
        $this->indicator = $indicator;
        $this->field = vdpModule::$fieldInfoService->validate($sourceAlias, $fieldAlias, $snapShot);
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getIndicator(): string
    {
        return $this->indicator;
    }

    /**
     * @return FieldInfo
     */
    public function getField(): FieldInfo
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }



}