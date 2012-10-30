<?php
/**
 * Date: 30.10.12
 * Time: 2:42
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config\Processor;

/**
 * Processor to support 'initialization' option
 */
class Initialization extends BaseProcessor
{

  /**
   * @param $id string
   * @param $config array
   * @param $appliedDrivers array
   * @return string|bool code to be added to generated file
   */
  public function apply($id, $config, $appliedDrivers)
  {
    if (isset($config['initialization']))
    {
      if ($config['initialization'] == 'instant')
      {
        $code = '// added by ' . __CLASS__ . PHP_EOL;
        $code .= sprintf("\$context->get(%s);\n", var_export($id, true));

        return $code;
      }
    }
  }
}
