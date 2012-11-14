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
      $default = null;
      if (strpos($value, '|') !== FALSE)
      {
        list($value, $default) = explode('|', $value, 2);
      }
      $parameter->setDefinitionCode(sprintf('sfConfig::get(%s,%s)', var_export($value, true), var_export($default, true)));
    }
  }
}
