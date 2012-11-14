<?php
/**
 * Date: 06.10.12
 * Time: 15:20
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\ParameterProcessor;

class sfConfigValue implements ParameterProcessor
{
  /**
   * @param \Axis\S1\ServiceContainer\Definition\ParameterDefinition $parameter
   * @return void
   */
  public function process($parameter)
  {
    if ($parameter->isDefined() && is_string($parameter->getValue()) && substr($parameter->getValue(),0,9) == 'config://')
    {
      $value = substr($parameter->getValue(),9);
      $parameter->setDefinitionCode(sprintf('sfConfig::get(%s)', var_export($value, true)));
    }
  }
}
