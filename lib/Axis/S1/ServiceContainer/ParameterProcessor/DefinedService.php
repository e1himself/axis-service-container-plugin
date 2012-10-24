<?php
/**
 * Date: 06.10.12
 * Time: 15:15
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\ParameterProcessor;

class DefinedService extends BaseParameterProcessor
{
  /**
   * @param \Axis\S1\ServiceContainer\Definition\ParameterDefinition $parameter
   * @return void
   */
  public function processParameter($parameter)
  {
    if ($parameter->isDefined() && is_string($parameter->getValue()))
    {
      if (substr($parameter->getValue(),0,10) == 'context://')
      {
        $service = substr($parameter->getValue(),10);
        $parameter->setDefinitionCode(sprintf('$context[%s]', var_export($service, true)));
      }
      elseif (substr($parameter->getValue(),0,1) == '@') // for Backward Compatibility with previous versions of plugin
      {
        $service = substr($parameter->getValue(),1);
        $parameter->setDefinitionCode(sprintf('$context[%s]', var_export($service, true)));
      }
    }
  }
}
