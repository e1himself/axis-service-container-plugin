<?php
/**
 * Date: 06.10.12
 * Time: 15:16
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\ParameterProcessor;

use \Axis\S1\ServiceContainer\Definition\ServiceDefinition;

abstract class BaseParameterProcessor
{
  /**
   * Should modify service definition object
   * @param \Axis\S1\ServiceContainer\Definition\ServiceDefinition $serviceDefinition
   * @return void
   */
  public function process($serviceDefinition)
  {
    foreach ($serviceDefinition->getParameters() as $parameter)
    {
      /** @var $parameter \Axis\S1\ServiceContainer\Definition\ParameterDefinition */
      if (!$parameter->isProcessed())
      {
        $this->processParameter($parameter);
      }
    }
  }

  /**
   * Should modify parameter definition
   * @param $parameter \Axis\S1\ServiceContainer\Definition\ParameterDefinition
   * @return void
   */
  abstract protected function processParameter($parameter);
}
