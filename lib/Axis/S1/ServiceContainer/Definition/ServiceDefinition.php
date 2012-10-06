<?php
/**
 * @author io
 */

namespace Axis\S1\ServiceContainer\Definition;

use Axis\S1\ServiceContainer\Definition\Exception\ParameterDoesNotExist;
use Axis\S1\ServiceContainer\Definition\ParameterDefinition;

class ServiceDefinition
{
  /**
   * @var string
   */
  protected $class;

  /**
   * @var string
   */
  protected $method;

  /**
   * @var array
   */
  protected $parameters = array();

  /**
   * @param $class string
   * @param array $parameters of ParameterDefinition objects
   */
  public function __construct($class, $parameters = array(), $method = '__construct')
  {
    $this->class = $class;
    $this->method = $method;
    $this->parameters = $parameters;
  }

  /**
   * @param $class
   * @return ServiceDefinition
   */
  public static function fromReflection($class, $method = '__construct')
  {
    // force global namespace
    if ($class[0] != '\\')
    {
      $class = '\\'.$class;
    }

    // $serviceDefinition = new static($class);
    $serviceDefinitionParameters = array();

    /* @var $reflection \ReflectionClass */
    $reflection = new \ReflectionClass($class);
    if (!$reflection->isInstantiable())
    {
      throw new \InvalidArgumentException("Class $class is not instantiable");
    }

    try {
      $constructor = $reflection->getMethod($method);

      if ($method != '__construct' && !$constructor->isStatic())
      {
        throw new \InvalidArgumentException("$class::$method is not static");
      }

      foreach ($constructor->getParameters() as $param)
      {
        /* @var $param \ReflectionParameter */

        $parameterDefinition = new ParameterDefinition($param->getName());
        if ($param->isDefaultValueAvailable())
        {
          $parameterDefinition->setDefaultValue($param->getDefaultValue());
        }

        $serviceDefinitionParameters[$param->getName()] = $parameterDefinition;
      }
    }
    catch (\ReflectionException $e)
    {
      if ($method !== '__construct') // not ok
      {
        throw $e;
      }
      // ok
      $serviceDefinitionParameters = array();
    }

    return new static($class, $serviceDefinitionParameters, $method);
  }

  public function getDefinitionCode()
  {
    $args = array();
    foreach ($this->getParameters() as $name => $parameter)
    {
      /** @var $parameter ParameterDefinition */
      $args[] = $parameter->getDefinitionCode();
    }

    if ($this->method == '__construct')
    {
      return "new {$this->class}(". implode(', ', $args) .")";
    }
    else
    {
      return "{$this->class}::{$this->method}(". implode(', ', $args). ")";
    }
  }

  /**
   * @param $name string
   * @return bool
   */
  public function hasParameter($name)
  {
    return array_key_exists($name, $this->parameters);
  }

  /**
   * @param $name string
   * @return ParameterDefinition
   * @throws Exception\ParameterDoesNotExist
   */
  public function getParameter($name)
  {
    if (!$this->hasParameter($name))
    {
      throw new ParameterDoesNotExist($name);
    }
    return $this->parameters[$name];
  }

  /**
   * @return array of ParameterDefinition objects
   */
  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * @param $method string
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }

  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getDefinitionCode();
  }
}
