<?php
/**
 * Date: 06.10.12
 * Time: 16:52
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer\Config\Processor;

class Tag extends BaseProcessor
{

  /**
   * @param $id string
   * @param $config array
   * @param $appliedDrivers array
   * @return string|bool code to be added to generated file
   */
  public function apply($id, $config, $appliedDrivers)
  {
    if (isset($config['tag']))
    {
      $tags = array($config['tag']);
    }
    elseif (isset($config['tags']))
    {
      $tags = (array)$config['tags'];
    }
    else
    {
      return false;
    }

    $code = '// added by ' . __CLASS__ . PHP_EOL;
    foreach ($tags as $tag)
    {
      $code .= sprintf(
        "\$serviceContainer->addTag(%s, %s);\n",
        var_export($id, true),
        var_export($tag, true)
      );
    }

    return $code;
  }
}