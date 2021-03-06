<?php
/**
 * Date: 06.10.12
 * Time: 16:50
 * Author: Ivan Voskoboynyk
 */
namespace Axis\S1\ServiceContainer\Config\Processor;

use \Axis\S1\ServiceContainer\Definition\ServiceDefinition;
use \Axis\S1\ServiceContainer\Definition\ArrayParameterDefinition;
use \Axis\S1\ServiceContainer\Definition\ParameterDefinition;

class Instantiate extends BaseProcessor
{
  /**
   * Cache
   * @var array
   */
  protected $parameterProcessors = array();

  /**
   * @param $id string
   * @param $config array
   * @param $appliedDrivers array
   * @return string|bool code to be added to generated file
   */
  public function apply($id, $config, $appliedDrivers)
  {
    if (isset($config['class']))
    {
      $serviceDefinition = $this->createServiceDefinition($config);

      foreach ($this->options->get('parameter_processors', array()) as $parameterProcessorId)
      {
        $parameterProcessor = $this->getParameterProcessor($parameterProcessorId);
        foreach ($serviceDefinition->getParameters() as $parameter)
        {
          $this->processServiceParameterDefinition($parameter, $parameterProcessor);
        }
      }

      $initialization = "function(\$context) { return {$serviceDefinition->getDefinitionCode()}; }";
      $isShared = isset($config['shared']) ? $config['shared'] : true; // shared by default

      if ($isShared)
      {
        $code = sprintf('$serviceContainer[%s] = $serviceContainer->share(%s);', var_export($id, true), $initialization) . PHP_EOL;
      }
      else
      {
        $code = sprintf('$serviceContainer[%s] = %s;', var_export($id, true), $initialization) . PHP_EOL;
      }

      return
        '// added by ' . __CLASS__ . PHP_EOL
        . $code;
    }

    return false;
  }

  /**
   * @param $config array
   * @return \Axis\S1\ServiceContainer\Definition\ServiceDefinition
   */
  protected function createServiceDefinition($config)
  {
    if (isset($config['method']))
    {
      $serviceDefinition = ServiceDefinition::fromReflection($config['class'], $config['method']);
    }
    else
    {
      $serviceDefinition = ServiceDefinition::fromReflection($config['class']);
    }

    $parameters = isset($config['param']) ? $config['param'] : (isset($config['parameters']) ? $config['parameters'] : array());
    foreach ($parameters as $name => $value)
    {
      try
      {
        $serviceDefinition->getParameter($name)->setValue($value);
      }
      catch (\Axis\S1\ServiceContainer\Definition\Exception\ParameterDoesNotExist $e)
      {
        trigger_error("Unexisting service parameter '{$e->getParameterName()}' defined in config file.");
      }
    }

    return $serviceDefinition;
  }


  /**
   * @param $class
   * @return \Axis\S1\ServiceContainer\ParameterProcessor\ParameterProcessor
   */
  protected function getParameterProcessor($class)
  {
    if (!isset($this->parameterProcessors[$class]))
    {
      $this->parameterProcessors[$class] = new $class();
    }
    return $this->parameterProcessors[$class];
  }

  /**
   * @param ParameterDefinition $parameter
   * @param \Axis\S1\ServiceContainer\ParameterProcessor\ParameterProcessor $parameterProcessor
   */
  public function processServiceParameterDefinition($parameter, $parameterProcessor)
  {
    if ($parameter instanceof ArrayParameterDefinition && $parameter->isArray())
    {
      /** $parameter ArrayParameterDefinition */
      $self = $this;
      $parameter->walk(function($part) use ($self, $parameterProcessor) {
        /** @var $self Instantiate */
        if ($part instanceof ParameterDefinition && !$part->isProcessed())
        {
          $self->processServiceParameterDefinition($part, $parameterProcessor);
        }
      });
    }
    elseif (!$parameter->isProcessed())
    {
      /** @var $parameter ParameterDefinition */
      $parameterProcessor->process($parameter);
    }
  }
}