<?php
/**
 * Date: 05.10.12
 * Time: 2:08
 * Author: Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer;

class TaggedServiceContainer extends ServiceContainer
{
  protected $tags = array();

  public function addTag($id, $tag)
  {
    $this->tags[$tag][] = $id;
    $this->tags[$tag] = array_unique($this->tags[$tag]);
  }

  /**
   * @param $tag string
   * @return array services
   */
  public function getByTag($tag)
  {
    $services = array();

    if (isset($this->tags[$tag]))
    {
      foreach ($this->tags[$tag] as $serviceName)
      {
        $services[] = parent::offsetGet($serviceName);
      }
    }

    return $services;
  }
}
