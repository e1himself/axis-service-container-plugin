<?php
/**
 * Date: 08.12.12
 * Time: 19:24
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config;

class Configurator extends BaseConfigurator
{
  /**
   * @return string
   */
  protected function getDefaultServiceContainerClass()
  {
    return '\\Axis\\S1\\ServiceContainer\\ServiceContainer';
  }

  /**
   * Generates configuration cache code
   * @return string Generated code
   */
  public function execute()
  {
    $config = $this->getConfiguration();
    $code = '';
    $code .= $this->generateContainerInitializationCode($config);
    $code .= $this->generateFactoriesInitializationCode($config);
    return $code;
  }

  protected function generateContainerInitializationCode(& $config)
  {
    if (isset($config['service_container']) && isset($config['service_container']['class']))
    {
      $ServiceContainerClass = $config['service_container']['class'];
      unset($config['service_container']);
    }
    else
    {
      $ServiceContainerClass = $this->getDefaultServiceContainerClass();
    }

    return "\$serviceContainer = new $ServiceContainerClass(\$this->factories);\n"
         . "\$this->factories = \$serviceContainer;\n"
         . "\$context = \$this;\n"
         . "\$serviceContainer['context'] = \$context;\n"
         . "\$serviceContainer['configuration'] = \$this->configuration;\n"
         . "\$serviceContainer['config_cache'] = \$this->configuration->getConfigCache();\n"
         . "\$serviceContainer['service_container'] = \$serviceContainer;\n"
         . "\$serviceContainer['dispatcher'] = \$this->dispatcher;\n";
  }

  /**
   * Generates
   *
   * @param $config
   * @return string
   * @throws \sfParseException
   */
  protected function generateFactoriesInitializationCode(& $config)
  {
    $code = '';
    $usedFactories = array();
    $appliedProcessors = array();

    $processors = $this->getOption('processors', array());
    foreach ($processors as $processorId => $processor)
    {
      /** @var $processor \Axis\S1\ServiceContainer\Config\Processor\BaseProcessor */
      if (is_string($processor))
      {
        $processor = new $processor();
      }
      elseif (is_array($processor))
      {
        if (!isset($processor['class']))
        {
          throw new \sfParseException("Class for '$processorId' service processor is not defined.");
        }
        $processor = new $processor['class'](isset($processor['options']) ? $processor['options'] : array());
      }

      foreach ($config as $factoryId => $factoryConfig)
      {
        // pass $factoryId, $factoryConfig and list of applied processors to current processor
        if ($generated = $processor->apply($factoryId, $factoryConfig, isset($appliedProcessors[$factoryId]) ? $appliedProcessors[$factoryId] : array()))
        {
          $usedFactories[$factoryId] = true;
          $appliedProcessors[$factoryId][] = $processorId;

          if ($generated !== true) // allow processor to return TRUE and not generate any code
          {
            $code .= $generated;
          }
        }
      }
    }

    $unusedFactories = array_diff(array_keys($config), array_keys($usedFactories));
    if (count($unusedFactories))
    {
      throw new \sfParseException('Not all factories are used: '.implode(', ', $unusedFactories));
    }

    return $code;
  }
}
