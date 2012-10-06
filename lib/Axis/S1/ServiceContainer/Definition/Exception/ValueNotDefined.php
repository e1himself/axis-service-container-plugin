<?php
/**
 * Date: 06.10.12
 * Time: 15:29
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Definition\Exception;

class ValueNotDefined extends \Exception
{
  /**
   * @var string
   */
  protected $parameterName;

  /**
   * @param string $parameterName
   * @param int $code
   * @param \Exception $previous
   */
  public function __construct($parameterName, $code = 0, Exception $previous = null)
  {
    $this->parameterName = $parameterName;
    $message = "Parameter '$parameterName' has no defined value.";
    parent::__construct($message, $code, $previous);
  }

  /**
   * @return string
   */
  public function getParameterName()
  {
    return $this->parameterName;
  }
}
