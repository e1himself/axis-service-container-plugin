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

  /**
   * @param string $id
   * @param string $tag
   * @return bool
   */
  public function hasTag($id, $tag)
  {
    return isset($this->tags[$tag]) && in_array($id, $this->tags[$tag]);
  }

  /**
   * @param string $id
   * @param string $tag
   */
  public function addTag($id, $tag)
  {
    $this->tags[$tag][] = $id;
    $this->tags[$tag] = array_unique($this->tags[$tag]);
  }

  /**
   * @param $tag string
   * @return array of defined objects
   */
  public function getByTag($tag)
  {
    $services = array();

    if (isset($this->tags[$tag]))
    {
      foreach ($this->tags[$tag] as $serviceName)
      {
        $services[$serviceName] = parent::offsetGet($serviceName);
      }
    }

    return $services;
  }

  /**
   * @param $tag string
   * @return array of defined objects keys
   */
  public function getKeysByTag($tag)
  {
    $services = array();

    if (isset($this->tags[$tag]))
    {
      foreach ($this->tags[$tag] as $serviceName)
      {
        $services[$serviceName] = $serviceName;
      }
    }

    return $services;
  }

  function offsetGet($id)
  {
    if ($id[0] == '#')
    {
      return $this->getByTag(substr($id,1));
    }
    return parent::offsetGet($id);
  }

  function offsetExists($id)
  {
    if ($id[0] == '#')
    {
      return true; // any tag is valid. even if there is no services we'll get an empty array
    }
    return parent::offsetExists($id);
  }

  public function offsetSet($id, $value)
  {
    parent::offsetSet($id, $value);
  }
}
