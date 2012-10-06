<?php
/**
 * Date: 06.10.12
 * Time: 16:51
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config\Processor;

abstract class BaseProcessor
{
  protected $options;

  public function __construct($options = array())
  {
    $this->options = new \sfParameterHolder();
    $this->options->add($options);
  }

  /**
   * @param $id string
   * @param $config array
   * @param $appliedDrivers array
   * @return string|bool code to be added to generated file
   */
  abstract public function apply($id, $config, $appliedDrivers);
}
