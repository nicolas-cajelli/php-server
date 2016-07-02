What?
=====
The goal of this module is to easily setup a rest server for any kind of project, without being tied up to any big project.
If you have ever worked on java using jetty, you're gonna be feel quite familiar with this module
 
Install
=======
Just add the dependency to your composer.json:
```
{
  "require": {
    "nicolascajelli/server": "x.y.z",
  },
  ...
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/nicolas-cajelli/php-server.git"
    }
  ]

}
```

Configure
=========

Create a Controller class. @Path is optional if you want to prefix all resources of given controller with the same path

```PHP
<?php
/**
 * @Controller
 * @Path "/myapi/mycontroller"
 */
class MyController {
    /**
     * @Path("/myresource")
     * @Method GET
     */
    publilc function getResource() {
    }
    
    /**
     * @Path("/myresource")
     * @Method POST
     */
    publilc function getResource() {
    }
}
```

The above example would create:
- GET /myapi/mycontroller/myresource
- POST /myapi/mycontroller/myresource

Build
=====

In order to be able to use the previously configured controllers, you need to build services and paths mappings.
To do so, just create a build file to be executed on every deploy:
```PHP
<?php

use nicolascajelli\server\build\MappingTask;

require_once 'vendor/autoload.php';

$task = new MappingTask(__DIR__);

```

Run
===

Create a handler for your project in order to be able to setup custom rules:
```PHP
<?php
use nicolascajelli\server\filesystem\ProjectStructure;
use nicolascajelli\server\RestHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MyHandler extends RestHandler
{
    public function __construct()
    {
        parent::__construct(new ContainerBuilder(), new ProjectStructure(MY_PROJECT_ROOT));
    }
}
```
 
Just start your handler and dispatch the request

```PHP
<?php
require_once 'vendor/autoload.php';
define('MY_PROJECT_ROOT', __DIR__);
$handler = new MyHandler();

$handler->dispatch();

```

Advanced
========

DI Services
-----------
All controllers are gonna be resolved by default on the build process, As well as any class referenced by resources and constructors.
If you want to define any other service, you have 2 annotations to be added to your class header:
- @NonSharedService To create a new instance every time the service is created
- @Inject to create regular services