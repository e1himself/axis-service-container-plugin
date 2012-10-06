<?php
/**
 * Date: 06.10.12
 * Time: 15:20
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\ParameterProcessor;

class RawValue extends BaseParameterProcessor
{
  /**
   * @param \Axis\S1\ServiceContainer\Definition\ParameterDefinition $parameter
   * @return void
   */
  protected function processParameter($parameter)
  {
    if ($parameter->isDefined() && is_string($parameter->getValue()) && substr($parameter->getValue(),0,6) == 'raw://')
    {
      $value = substr($parameter->getValue(),6);
      $parameter->setDefinitionCode(var_export($value, true));
    }
  }
}
