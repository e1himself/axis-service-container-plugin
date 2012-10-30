<?php
/**
 * Date: 06.10.25
 * Time: 02:12
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\ParameterProcessor;

class GetByTag extends BaseParameterProcessor
{
  /**
   * @param \Axis\S1\ServiceContainer\Definition\ParameterDefinition $parameter
   * @return void
   */
  public function processParameter($parameter)
  {
    if ($parameter->isDefined() && is_string($parameter->getValue()))
    {
      if (substr($parameter->getValue(),0,6) == 'tag://')
      {
        $tag = substr($parameter->getValue(),6);
        $parameter->setDefinitionCode(sprintf('$context[\'service_container\']->getByTag(%s)', var_export($tag, true)));
      }
    }
  }
}
