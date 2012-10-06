<?php
/**
 * @author Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config\Handler;

class FactoryConfigHandler extends \sfFactoryConfigHandler
{
  public static $STD_FACTORIES = array('view_cache_manager', 'logger', 'i18n', 'controller', 'request', 'response', 'routing', 'storage', 'user', 'view_cache', 'mailer');

  protected function getDefaultServiceContainerClass()
  {
    return '\\Axis\\S1\\ServiceContainer\\ServiceContainer';
  }

  /**
   * @param array $configFiles
   * @return string generated code
   * @throws \sfParseException if not all factories are used
   */
  public function execute($configFiles)
  {
    $code = parent::execute($configFiles);
    $config = static::getConfiguration($configFiles);

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

    $class = __CLASS__;
    return "// added by $class \n"
        . "\$serviceContainer = new $ServiceContainerClass(\$this->factories);\n"
        . "\$this->factories = \$serviceContainer;\n"
        . "\$context = \$this;\n"
        . "\$serviceContainer['context'] = \$context;\n"
        . "\$serviceContainer['configuration'] = \$this->configuration;\n"
        . "\$serviceContainer['service_container'] = \$serviceContainer;\n";
  }

  protected function generateFactoriesInitializationCode(& $config)
  {
    $code = '';
    $usedFactories = array();
    $appliedProcessors = array();

    $processors = $this->getParameterHolder()->get('processors', array());
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
          throw new \sfParseException("Class for '$processorId'' service processor is not defined.");
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

  /**
   * @param array $configFiles
   * @return array configuration
   */
  static public function getConfiguration(array $configFiles)
  {
    $configuration = parent::getConfiguration($configFiles);

    // remove std factories from configuration
    foreach (static::$STD_FACTORIES as $std)
    {
      if (array_key_exists($std, $configuration))
      {
        unset ($configuration[$std]);
      }
    }

    return $configuration;
  }
}
