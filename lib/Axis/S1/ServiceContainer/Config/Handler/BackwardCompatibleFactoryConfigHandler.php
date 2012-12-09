<?php
/**
 * @author Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config\Handler;

use Axis\S1\ServiceContainer\Config\ConfiguratorInterface;

class BackwardCompatibleFactoryConfigHandler extends \sfFactoryConfigHandler
{
  /**
   * @param array $configFiles
   * @return string generated code
   * @throws \sfParseException if not all factories are used
   */
  public function execute($configFiles)
  {
    $code = parent::execute($configFiles);
    $config = static::getConfiguration($configFiles);

    // initialize configurator
    $configuratorClass = $this->getParameterHolder()->get('configurator_class', '\\Axis\\S1\\ServiceContainer\\Config\\Configurator');
    /** @var $configurator ConfiguratorInterface */
    $configurator = new $configuratorClass();
    $configurator->setConfiguration($config);
    $configurator->setOptions($this->getParameterHolder()->get('configurator_options', array()));

    $class = __CLASS__;
    $code .= "// added by $class\n" . $configurator->execute();

    return $code;
  }
}
