<?php
/**
 * Date: 06.10.12
 * Time: 15:16
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\ParameterProcessor;

interface ParameterProcessor
{
  /**
   * Should modify parameter definition
   * @param $parameter \Axis\S1\ServiceContainer\Definition\ParameterDefinition
   * @return void
   */
  public function process($parameter);
}
