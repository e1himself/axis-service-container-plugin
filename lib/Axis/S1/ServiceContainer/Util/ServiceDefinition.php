<?php
/**
 * @author io
 */

namespace Axis\S1\ServiceContainer\Util;

use Axis\Toolkit\ArrayUtils;

class ServiceDefinition
{
  protected $class;
  protected $parameters = array();

  protected $isSingleton = false;

  protected $containerTemplate = '$container[%s]';

  public function __construct($class, $parameters = array())
  {
    $this->class = $class;
    $this->parameters = $parameters;
  }

  public function setIsSingleton($value = true)
  {
    $this->isSingleton = $value;
  }

  public function setContainerTemplate($containerTemplate)
  {
    if (strpos($containerTemplate, '%s') === FALSE)
    {
      throw new \InvalidArgumentException("Template should contain %s for requested service name substitution (for example: \$container[%s]");
    }

    $this->containerTemplate = $containerTemplate;
  }

  protected function getArgumentCode($argument)
  {
    if (is_string($argument) && substr($argument,0,1) === '@') //&& in_array(substr($argument,1), $this->existingServices
    {
      $service = substr($argument,1);
      return sprintf($this->containerTemplate, var_export($service, true));
    }
    else
    {
      return var_export($argument, true);
    }
  }

  public function getDefinition()
  {
    $class = $this->class;

    // param -> parameters fallback / for backward compatibility
    $parameters = $this->parameters;

    $reflection = new \ReflectionClass($class);
    /* @var $reflection \ReflectionClass */

    $used = array();
    $unused = array_keys($parameters);

    try {

      $constructor = $reflection->getMethod($this->isSingleton ? 'getInstance' : '__construct');
      $newInstanceArgs = array();

      if (ArrayUtils::isAssociative($parameters))
      {
        $emptyLastArgs = 0;

        foreach ($constructor->getParameters() as $param)
        {
          /* @var $param ReflectionParameter */
          $key = $param->getName();
          if (!array_key_exists($key, $parameters))
          {
            $emptyLastArgs++;
            if ($param->isDefaultValueAvailable())
            {
              $newInstanceArgs[$key] = var_export($param->getDefaultValue(), true);
            }
            else
            {
              $newInstanceArgs[$key] = 'null';
            }
          }
          else
          {
            $emptyLastArgs = 0;
            $used[] = $key;
            $newInstanceArgs[$key] = $this->getArgumentCode($parameters[$key]);
          }
        }

        if ($emptyLastArgs > 0) array_splice($newInstanceArgs, -$emptyLastArgs);

        // handle unused parameters
        $unused = array_diff(array_keys($parameters), $used);
      }
      else // parameters array is not a hash map
      {
        foreach ($parameters as $param)
        {
          $newInstanceArgs[] = $this->getArgumentCode($param);
        }
        array_splice($unused, count($newInstanceArgs));
      }
    }
    catch (\ReflectionException $e)
    {
      $newInstanceArgs = array();
    }

    if (count($unused) > 0)
    {
      trigger_error('Not all parameters are used for class ' . $this->class . ': ' . implode(', ', $unused), E_USER_NOTICE);
    }

    // force global namespace
    if ($class[0] != '\\') $class = '\\'.$class;

    if ($this->isSingleton)
    {
      $construction = "$class::getInstance(". implode(', ', $newInstanceArgs) .")\n";
    }
    else
    {
      $construction = "new $class(". implode(', ', $newInstanceArgs) .")\n";
    }

    return $construction;
  }

  public function __toString()
  {
    return $this->getDefinition();
  }
}
