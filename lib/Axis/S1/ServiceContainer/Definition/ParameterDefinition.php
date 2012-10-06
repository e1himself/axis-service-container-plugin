<?php
/**
 * Date: 06.10.12
 * Time: 14:52
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Definition;

use \Axis\S1\ServiceContainer\Definition\Exception\ValueNotDefined;

class ParameterDefinition
{
  /**
   * @var string
   */
  protected $name;

  /**
   * @var mixed
   */
  protected $value;
  /**
   * @var mixed
   */
  protected $defaultValue;
  /**
   * @var bool
   */
  protected $hasDefaultValue = false;

  /**
   * @var bool
   */
  protected $isDefined = false;
  /**
   * @var bool
   */
  protected $isProcessed = false;
  /**
   * @var string
   */
  protected $code;

  public function __construct($name)
  {
    $this->name = $name;
  }

  /**
   * @param $name string
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return bool
   */
  public function isProcessed()
  {
    return $this->isProcessed;
  }

  /**
   * @return bool
   */
  public function isDefined()
  {
    return $this->isDefined;
  }

  /**
   * @return bool
   */
  public function hasDefaultValue()
  {
    return $this->hasDefaultValue;
  }

  /**
   * @param $defaultValue mixed
   */
  public function setDefaultValue($defaultValue)
  {
    $this->hasDefaultValue = true;
    $this->defaultValue = $defaultValue;
  }

  /**
   * @return mixed
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }

  /**
   * @param $value mixed
   */
  public function setValue($value)
  {
    $this->isDefined = true;
    $this->value = $value;
  }

  /**
   * @return mixed
   * @throws ValueNotDefined
   */
  public function getValue()
  {
    if ($this->isDefined)
    {
      return $this->value;
    }
    if ($this->hasDefaultValue)
    {
      return $this->defaultValue;
    }
    throw new ValueNotDefined($this->getName());
  }

  /**
   * @param $code string
   */
  public function setDefinitionCode($code)
  {
    $this->isProcessed = true;
    $this->code = $code;
  }

  /**
   * @return string
   */
  public function getDefinitionCode()
  {
    if ($this->isProcessed)
    {
      return $this->code;
    }
    return $this->getDefaultDefinitionCode();
  }

  /**
   * Generates default parameter value definition code
   *
   * @return string
   */
  protected function getDefaultDefinitionCode()
  {
    return var_export($this->getValue(), true);
  }
}
