<?php
/**
 * @author Ivan Voskoboynyk
 */

namespace Axis\S1\ServiceContainer;

class ServiceContainer extends \Pimple
{
  /**
   * Previous service container object
   * @var \ArrayAccess
   */
  protected $fallback;

  /**
   * @param $fallback
   */
  function __construct($fallback)
  {
    $this->fallback = $fallback;
  }

  function offsetGet($id)
  {
    if ($id == 'mailer' && !parent::offsetExists($id))
    {
      return $this['context']->getMailer();
    }
    if (!parent::offsetExists($id) && isset($this->fallback[$id]))
    {
      return $this->fallback[$id];
    }
    return parent::offsetGet($id);
  }

  function offsetExists($id)
  {
    return parent::offsetExists($id) || isset($this->fallback[$id]);
  }
}
