<?php
/**
 * Date: 08.12.12
 * Time: 19:27
 * Author: Ivan Voskoboynyk
 */
namespace Axis\S1\ServiceContainer\Config;

interface ConfiguratorInterface
{
  /**
   * Generates configuration cache code
   * @return string Generated code
   */
  public function execute();

  /**
   * @param array $configuration
   */
  public function setConfiguration($configuration);

  /**
   * @param array $options
   */
  public function setOptions($options);

  /**
   * @param string $key
   * @param mixed $value
   */
  public function setOption($key, $value);
}
