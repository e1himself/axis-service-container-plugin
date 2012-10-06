<?php
/**
 * Date: 06.10.12
 * Time: 18:18
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Definition\Exception;

class ParameterDoesNotExist extends \Exception
{
  protected $parameterName;

  public function __construct($name, $code = 0, Exception $previous = null)
  {
    $message = "Parameter '$name' does not exist in current service definition constructor";
    $this->parameterName = $name;
    parent::__construct($message, $code, $previous);
  }

  public function getParameterName()
  {
    return $this->parameterName;
  }
}
