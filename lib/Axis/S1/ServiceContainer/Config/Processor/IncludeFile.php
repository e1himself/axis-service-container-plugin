<?php
/**
 * Date: 06.10.12
 * Time: 17:55
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config\Processor;

class IncludeFile extends BaseProcessor
{

  /**
   * @param $id string
   * @param $config array
   * @param $appliedDrivers array
   * @return string|bool code to be added to generated file
   * @throws \sfParseException
   */
  public function apply($id, $config, $appliedDrivers)
  {
    if (isset($config['file']))
    {
      // we have a file to include
      if (!is_readable($config['file']))
      {
        // factory file doesn't exist
        throw new \sfParseException(sprintf('Configuration file specifies nonexistent or unreadable file "%s".', $config['file']));
      }
      // return code to append
      return
        '// added by ' . __CLASS__ . PHP_EOL
        . sprintf("require_once('%s');\n", $config['file']);
    }

    return false;
  }
}
