<?php
/**
 * Date: 06.10.12
 * Time: 16:50
 * Author: Ivan Voskoboynyk
 */
namespace Axis\S1\ServiceContainer\Config\Processor;

use \Axis\S1\ServiceContainer\Definition\ServiceDefinition;

class Instantiate extends BaseProcessor
{
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

      foreach ($this->options->get('parameter_processors', array()) as $parameterProcessor)
      {
        /** @var $parameterProcessor \Axis\S1\ServiceContainer\ParameterProcessor\BaseParameterProcessor */
        $parameterProcessor = new $parameterProcessor();
        $parameterProcessor->process($serviceDefinition);
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
}