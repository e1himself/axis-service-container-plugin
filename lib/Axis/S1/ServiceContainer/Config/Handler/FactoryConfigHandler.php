<?php
/**
 * @author Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config\Handler;

use Axis\S1\ServiceContainer\Config\ConfiguratorInterface;

class FactoryConfigHandler extends \sfFactoryConfigHandler
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
    $configurator->setOptions($this->getParameterHolder()->getAll());

    $class = __CLASS__;
    // prepend ServiceContainer generated code before usual sfFactoryConfigHandler output
    $code = "<?php\n"
    . "// added by $class\n" . $configurator->execute() . "\n"
    . '?>' . $code;

    // Eliminate unneeded CLOSING/OPENING (the most reliable way to hack into parent generated code)
    $code = str_replace('?><?php', '', $code);

    return $code;
  }
}
