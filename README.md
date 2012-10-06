AxisServiceContainerPlugin
==========================

Requirements
------------

This plugin requires `symfony/class-loader` component to be included in project.

To retrieve dependencies just run `composer.phar install` in the plugin root directory.

### Additional options

#### `initialization` option (default is `null`)

Allows to control when the factory should be initialized. The only supported value is `instant` - initialize instantly (on context creating).
Everything else makes the factory to be called on the first request.

#### `shared` option (defualt is `true`)

Allows to control if the factory should be instantiated every time it is requested or the object is shared between all requests.