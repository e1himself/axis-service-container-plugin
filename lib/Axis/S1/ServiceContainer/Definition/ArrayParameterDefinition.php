<?php
/**
 * Date: 24.10.12
 * Time: 22:37
 * Author: Ivan Voskoboynyk
 */
namespace Axis\S1\ServiceContainer\Definition;

use \Axis\S1\ServiceContainer\Definition\Exception\ValueNotDefined;

class ArrayParameterDefinition extends ParameterDefinition
{
  /**
   * @var array
   */
  protected $struct = null;

  /**
   * @return bool
   */
  public function isArray()
  {
    return is_array($this->value);
  }

  /**
   * @param mixed $value
   */
  public function setValue($value)
  {
    if (is_array($value))
    {
      $this->struct = $this->_transformArray($value);
    }
    parent::setValue($value);
  }

  /**
   * @param array $array
   * @return array
   */
  protected function _transformArray($array)
  {
    $transformed = array();

    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
        $transformed[$key] = $this->_transformArray($value);
      }
      else
      {
        $param = new ParameterDefinition($key);
        $param->setValue($value);

        $transformed[$key] = $param;
      }
    }

    return $transformed;
  }

  /**
   * @param array $array
   * @param callable $callback
   * @param callable|null $reduce
   * @return array|mixed
   */
  protected function _deepWalk(& $array, $callback, $reduce = null)
  {
    $result = array();
    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
        $result[$key] = $this->_deepWalk($value, $callback, $reduce);
      }
      else
      {
        $result[$key] = call_user_func($callback, $value);
      }
    }

    if ($reduce)
    {
      $result = call_user_func($reduce, $result);
    }
    return $result;
  }

  /**
   * @param callable $callback
   * @param callable|null $reduce
   * @return array
   */
  public function walk($callback, $reduce = null)
  {
    return $this->_deepWalk($this->struct, $callback, $reduce);
  }

  /**
   * @return string
   */
  public function getDefinitionCode()
  {
    if ($this->isArray())
    {
      $code = $this->walk(function($item) {
        return $item->getDefinitionCode();
      }, function($array) {
        $definition = array();
        foreach ($array as $key => $value)
        {
          $definition[] = sprintf('%s => %s', var_export($key, true), $value );
        }
        return 'array('.implode(', ',$definition).')';
      });
      return $code;
    }
    else
    {
      return parent::getDefinitionCode();
    }
  }
}
