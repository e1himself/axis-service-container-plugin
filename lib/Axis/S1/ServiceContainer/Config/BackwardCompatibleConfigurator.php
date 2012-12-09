<?php
/**
 * Date: 08.12.12
 * Time: 19:58
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config;

class BackwardCompatibleConfigurator extends Configurator
{
  public static $STD_FACTORIES = array('view_cache_manager', 'logger', 'i18n', 'controller', 'request', 'response', 'routing', 'storage', 'user', 'view_cache', 'mailer');

  public function getConfiguration()
  {
    $configuration = parent::getConfiguration();

    $skip = $this->getOption('skip_factories', null);
    if ($skip === null)
    {
      $skip = static::$STD_FACTORIES;
    }
    else
    {
      $skip = array_keys(array_filter($skip, 'intval'));
    }

    // remove std factories from configuration
    foreach ($skip as $std)
    {
      if (array_key_exists($std, $configuration))
      {
        unset ($configuration[$std]);
      }
    }

    return $configuration;
  }
}
