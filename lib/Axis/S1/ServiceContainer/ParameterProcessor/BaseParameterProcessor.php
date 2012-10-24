<?php
/**
 * Date: 06.10.12
 * Time: 15:16
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\ParameterProcessor;

use \Axis\S1\ServiceContainer\Definition\ServiceDefinition;
use \Axis\S1\ServiceContainer\Definition\ArrayParameterDefinition;
use \Axis\S1\ServiceContainer\Definition\ParameterDefinition;

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
      if ($parameter instanceof ArrayParameterDefinition && $parameter->isArray())
      {
        /** $parameter ArrayParameterDefinition */
        $parameter->walk(array($this, 'processArrayParameterPart'));
      }
      elseif (!$parameter->isProcessed())
      {
        /** @var $parameter ParameterDefinition */
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

  public function processArrayParameterPart($part)
  {
    if ($part instanceof ParameterDefinition && !$part->isProcessed())
    {
      $this->processParameter($part);
    }
  }
}
