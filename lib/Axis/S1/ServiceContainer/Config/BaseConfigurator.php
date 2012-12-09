<?php
/**
 * Date: 08.12.12
 * Time: 19:16
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config;

abstract class BaseConfigurator implements ConfiguratorInterface
{
  /**
   * @var array
   */
  protected $options = array();

  /**
   * @var array
   */
  protected $configuration;

  /**
   * @param array $configuration
   * @param array $options
   */
  public function __construct($configuration = array(), $options = array())
  {
    $this->setConfiguration($configuration);
    $this->setOptions($options);
  }

  /**
   * @param array $configuration
   */
  public function setConfiguration($configuration)
  {
    $this->configuration = $configuration;
  }

  /**
   * @return array
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }

  /**
   * @param array $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }

  /**
   * @return array
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function getOption($key, $default = null)
  {
    return isset($this->options[$key]) ? $this->options[$key] : $default;
  }

  /**
   * @param string $key
   * @param mixed $value
   */
  public function setOption($key, $value)
  {
    $this->options[$key] = $value;
  }
}
