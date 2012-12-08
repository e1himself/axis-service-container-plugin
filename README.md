AxisServiceContainerPlugin
==========================

This plugin allows to add any amount of factories configuration to symfony factories.yml using [Pimple](http://pimple.sensiolabs.org/) as service container.

Also it adds a rich functionality for instantiating and configuring factories.

Installation
------------

### Composer way

Just add `axis/axis-service-container-plugin` dependency to your `composer.json`:
```json
  "require": {
    "axis/axis-service-container-plugin": "dev-master"
  }
```

Defining Services
-----------------

### Basic definition

You can define any services like the standard symfony `factories.yml` does.
Assume that you have your service implemented in `MyBasicServiceImplementation` class:

```php
class MyBasicServiceImplementation implements MyService
{
  public function doSomething()
  {
    echo 'Yep. It works!';
  }
}
```

And you want you define it using symfony context factories. So you need just to add its instantiation configuration to your `factories.yml`:

```yml
  my_service:
    class: MyBasicServiceImplementation
```

Now you can retrieve an instance of that class in your code:

```php
/** @var $myService MyService */
$myService = sfContext:getInstance()->get('my_service');
$myService->doSomething(); // echoes "Yep. It works!"
```

----------------------------------
*Note*: all services are stored to [**Pimple**](http://pimple.sensiolabs.org/) service container using `share` method. This means that each service is a [shared objects](http://pimple.sensiolabs.org/#defining-shared-objects) instantiated only once it was requested first time.

### Definition with parameters

Assume you want to instantiate a `MyParamServiceImplementation` class that have a parameterized constructor.

```php
class MyParamServiceImplementation implements MyService
{
  protected $greating;

  public funciton __construct($greating = 'Yep. It works!')
  {
    $this->greating = $greating;
  }
  public function doSomething()
  {
    echo $this->greating;
  }
}
```

You can use `parameters` configuration option listing all constructor parameters in *any* order but preserving *exact names*:

```yml
  my_service:
    class: MyParamServiceImplementation
    parameters:
      greating: "Hooray!"
```

The usage is the same:

```php
/** @var $myService MyService */
$myService = sfContext:getInstance()->get('my_service');
$myService->doSomething(); // echoes "Hooray!"
```

### Instant initialization

If your service should be instantiated (and thereby initialized) on startup you can use `initialization` config parameter. The only value the plugin supports is `instant`. Any other value is treated as undefined and meaningless.

```yml
  my_service:
    class: MyBasicServiceImplementation
    initialization: instant
```

By defining `initialization: instant` you tell that `my_service` should be instantiated just after the symfony context is created.

### Including file

If your service class is not loaded automatically with symfony autoloader or any other configured autoloaders you can use `file` configuration option to tell symfony to include that file on context creation.

```yml
  my_service:
    class: MyServiceImplementation
    file:  %SF_ROOT_DIR%/lib/vendor/my_company/MyServiceImplementation.php
```

### Tagging

You can mark your services with tags. This allows your to retrieve all defined services from context that have a specific tag assigned. Use `tag` option.

```yml
  my_service1:
    class: MySimpleService
    tag: greater

  my_service2:
    class: MyAdvancedService
    tag: greater
```

After this you can retreive all services from context using a hash-prefixed tag name:

```php
/** @var $services array */
$services = sfContext:getInstance()->get('#greater');
var_dump(array_map('get_class', $services)); 
// will output "array('MySimpleService', 'MyAdvancedService')"
```

Service Parameters
------------------

When defining services sometimes you need to define constructor parameters values. Sometimes it is not enough to use just constant values. AxisServiceContainerPlugin allows you to use advanced parameter processing.

### Config value
If you want instantiate a service with a config value passed as parameter you can use `config` parameter processor:

```yml
  my_service:
    class: MyParamServiceImplementation
    parameters:
      greating: config://app_my_service_greating

```

It will instantiate `my_service` passing `sfConfig::get('app_my_service_greating')` value as `$greating` parameter.

#### Config value with default
Also you can use default value for config getter:

```yml
  my_service:
    class: MyParamServiceImplementation
    parameters:
      greating: config://app_my_service_greating|Wow! It supports default value!

```

This code will instantiate `my_service` passing `sfConfig::get('app_my_service_greating', "Wow! It supports default value!")` value as `$greating` parameter.

### Defined service
Sometimes its handy to pass any other defined service to your service constructor as parameter.

```yml
  my_service_transport:
    class: SoapClient
    parameters:
      wsdl:  http://services.mycompany.com/?WSDL

  my_service:
    class: MyRemoteService
    parameters:
      transport: context://my_service_transport
```

Now retrieving `my_service` from context service container will return a `my_service` service instance with `my_service_transport` service instance passed as `$transport` parameter to its constructor.

### Defined services with a specific tag
Sometimes you may want to pass to service constructor a collection of services with a specific tag assigned. We can do that!

```yml
  my_service.extension.a:
    class: MyServiceExtensionA
    tag: my_service.extension
  
  my_service.extension.b:
    class: MyServiceExtensionA
    tag: my_service.extension

  my_service: 
    class: MyService
    parameters:
      extensions: tag://my_service.extension
```

### Raw value
And if you want to pass actual string value prefixed with special words and leave it unprocessed use `raw` prefix:

```yml
  my_service:
    class: MyService
    parameters:
      greating: raw://config://This doesn't mean anything. It's just a string value
```

### Parameters within arrays
You can use any smart parameters processing in arrays passed as parameter values. For example you can do this:

```yml 
  my_service:
    class: MyService
    parameters:
      options:
        transport: context://my_service_transport
        name: config://app_my_service_name|MyService
```

Declared services
-----------------

Service container has declared services at the very beginning. They include standard symfony factories and 
core context entities.

### Standard symfony services

* `view_cache_manager`
* `logger`
* `i18n`
* `controller`
* `request`
* `response`
* `routing`
* `storage`
* `user`
* `view_cache`
* `mailer`

All of this services can be used as initialization parameter using context:// parameter processor.

### Additional plugin services

Additionally plugin appends to the service container next services:
* `context` - symfony context instance
* `configuration` - current application configuration
* `dispatcher` - symfony event dispatcher
* `service_container` - the service container instance itself (useful for using service container public API 
  inaccessible via sfContext instance)

Known issues
------------
* You cannot configure [standard symfony services](#standard-symfony-services) using all plugin features. 
  Standard services are handled by default symfony sfFactoriesConfigHandler.