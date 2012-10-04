<?php
/**
 * @author io
 */

namespace Axis\S1\ServiceContainer\Config;

use \Axis\S1\ServiceContainer\Util\ServiceDefinition;

class FactoryConfigHandler extends \sfFactoryConfigHandler
{
  protected $existingServices = array();

  public function execute($configFiles)
  {
    $code = parent::execute($configFiles);

    $class = __CLASS__;
    $config = self::getConfiguration($configFiles);
    $std = array('view_cache_manager', 'logger', 'i18n', 'controller', 'request', 'response', 'routing', 'storage', 'user', 'view_cache', 'mailer');
    $this->existingServices = $std;

    $code .=
      '// added by ' . get_class($this) . "\n"
      . "\$serviceContainer = new \\Axis\\S1\\ServiceContainer\\ServiceContainer(\$this->factories);\n"
      . "\$this->factories = \$serviceContainer;\n"
      . "\$context = \$this;\n"
      . "\$serviceContainer['context'] = \$context;\n"
      . "\$serviceContainer['configuration'] = \$this->configuration;\n"
      . "\$serviceContainer['service_container'] = \$serviceContainer;\n";

    foreach ($config as $factoryKey => $factoryConfig)
    {
      if (!in_array($factoryKey, $std)) // process only non-system factories
      {
        $this->existingServices[] = $factoryKey;

        $initializationCode = $this->getFactoryInitializationCode($factoryKey, $factoryConfig);
        if ($initializationCode) $code .= "\n\n// $class: $factoryKey factory initialization code\n".$initializationCode."\n";
      }
    }

    foreach ($config as $factoryKey => $factoryConfig)
    {
      if (!in_array($factoryKey, $std))
      {
        if (isset($factoryConfig['initialization']) && $factoryConfig['initialization'] == 'instant')
        {
          $code .= "\n\n// $class: @$factoryKey instant initialization\n\$initializedService = \$serviceContainer['$factoryKey'];";
        }
      }
    }

    return $code;
  }

  protected function getFactoryInitializationCode($factory, $config)
  {
    $class = $config['class'];

    $isShared = isset($config['shared']) ? $config['shared'] : true;

    // param -> parameters fallback / for backward compatibility
    $params = isset($config['param']) ? $config['param']  : (isset($config['parameters']) ? $config['parameters'] : array());

    $initialization = new ServiceDefinition($class, $params);
    $initialization->setContainerTemplate('$context[%s]');

    $code = "function() use (\$context) {\n"
      . 'return '. $initialization->getDefinition() .";\n"
      . "}";

    return "\$serviceContainer['$factory'] = " . ($isShared ? "\$serviceContainer->share($code)" : $code) . ";\n";
  }
//
//  protected function getArgumentCode($argument)
//  {
//    if (is_string($argument) && substr($argument,0,1) === '@' && in_array(substr($argument,1), $this->existingServices))
//    {
//      $service = substr($argument,1);
//      return "\$context['$service']";
//    }
//      else
//    {
//      return var_export($argument, true);
//    }
//  }
}
