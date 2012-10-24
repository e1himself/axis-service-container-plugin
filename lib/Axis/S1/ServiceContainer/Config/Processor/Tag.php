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
    $tags = array();
    if (isset($config['tag']))
    {
      $tags = array_merge($tags, (array)$config['tag']);
    }
    if (isset($config['tags']))
    {
      $tags = array_merge($tags, (array)$config['tags']);
    }

    if (count($tags) == 0)
    {
      return false;
    }

    $code = '// added by ' . __CLASS__ . PHP_EOL;
    foreach (array_unique($tags) as $tag)
    {
      if (strlen($tag) > 0) // forbid empty tags
      {
        $code .= sprintf(
          "\$serviceContainer->addTag(%s, %s);\n",
          var_export($id, true),
          var_export($tag, true)
        );
      }
    }

    return $code;
  }
}