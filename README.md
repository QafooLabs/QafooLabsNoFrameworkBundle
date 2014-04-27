# QafooLabs FrameworkExtraBundle

**Note** This bundle is in development at the moment. Use at own risk, API can change.

## Goals

We want to achieve slim controllers that are registered as a service.  The
number of services required in any controller should be very small (2-4).  We
believe Context to controllers should be explicitly passed to avoid hiding it
in services.

Ultimately this should make Controllers testable with lightweight unit- and
integration tests.  Elaborate seperation of Symfony from your business logic
should become unnecessary by building controllers that don't depend on Symfony
from the beginning (except maybe Request/Response classes).

For this reason the following features are provided by this bundle:

- Returning View data from controllers
- Returning RedirectRouteResponse
- Helper for Controllers as Service
- Convert Exceptions from Domain/Library Types to Framework Types
- JMS Serializer as Templating Engine

Roadmap:

- FormRequest Handling
- Explicit FrameworkContext
- Widgets

## Returning View data from controllers

### Returning Arrays

This bundle replaces the ``@Extra\Template()`` annotation support
from the Sensio FrameworkExtraBundle, without requiring to add the annotation
to the controller actions.

You can just return arrays from controllers and the template names will
be inferred from Controller+Action-Method names.

```php
<?php
# src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

class DefaultController
{
    public function helloAction($name = 'Fabien')
    {
        return array('name' => $name);
    }
}
```

### Returning TemplateView

Two use-cases sometimes occur where returning an array from the controller is not flexible enough:

1. Rendering a template with a different action name.
2. Adding headers to the Response object

For this case you can change the previous example to return a ``TemplateView`` instance:

```php
<?php
# src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

use QafooLabs\Views\TemplateView;

class DefaultController
{
    public function helloAction($name = 'Fabien')
    {
        return new TemplateView(
            array('name' => $name),
            'hallo', // AcmeDemoBundle:Default:hallo.html.twig instead of hello.html.twig
            201,
            array('X-Foo' => 'Bar')
        );
    }
}
```

**Note:** Contrary to the ``render()`` method on the default Symfony base controller
here the view parameters and the template name are exchanged. This is because
everything except the view parameters are optional.

### Returning ViewModels

Usually controllers quickly gather view related logic that is not properly
extracted into a Twig extension, because of the insignficance of these data
transforming methods. This is why on top of the returning array support you can
also use view models and return them from your actions.

Each view model is a class that maps to exactly one template and can contain
properties + methods that are available under the ``view`` template name in
Twig using the same resolving mechanism as if you are returing arrays.

A view model can be any class as long as it does not extend the Symfony
Response class.

```php
<?php
# src/Acme/DemoBundle/View/Default/HelloView.php
namespace Acme\DemoBundle\View\Default;

class HelloView
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getReversedName()
    {
        return strrev($this->name);
    }
}
```

In your controller you just return the view model:

```php
<?php
# src/Acme/DemoBundle/Controller/HelloController.php

namespace Acme\DemoBundle\Controller;

class HelloController
{
    public function helloAction($name)
    {
        return new HelloView($name);
    }
}
```

It gets rendered as ``AcmeBundle:Hello:hello.html.twig``,
where the view model is available as the ``view`` twig variable:

```
Hello {{ view.name }} or {{ view.reversedName }}!
```

You can optionally extend from ``QafooLabs\Views\ViewStruct``.
Every ``ViewStruct`` implementation has a constructor accepting and setting
key-value pairs of properties that exist on the view model class.

## RedirectRouteResponse

Redirecting in Symfony is much more likely to happen internally to a given
route. The ``QafooLabs\Views\RedirectRouteResponse`` can be returned from
your controller and a listener will turn it into a proper Symfony ``RedirectResponse``:

```php
<?php
# src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

use QafooLabs\Views\RedirectRouteResponse;

class DefaultController
{
    public function redirectAction()
    {
        return new RedirectRouteResponse('hello', array(
            'name' => 'Fabien'
        ));
    }
}
```

## Helper for Controllers as Service

We added a ``controller_utils`` service that offers the functionality
of the Symfony base controller plus some extras.

## Convert Exceptions

Usually the libraries you are using or your own code throw exceptions that can be turned
into HTTP errors other than the 500 server error. To prevent having to do this in the controller
over and over again you can configure to convert those exceptions in a listener:

    qafoo_labs_framework_extra:
        convert_exceptions:
            Doctrine\ORM\EntityNotFoundException: Symfony\Component\HttpKernel\Exception\NotFoundHttpException
            Doctrine\ORM\ORMException: 500

Notable facts about the conversion:

- Both Target Exception classes or just a HTTP StatusCode can be specified
- Subclasses are checked for as well.
- If you don't define conversions the listener is not registered.
- If an exception is converted the original exception will specifically logged
  before conversion. That means when an exception occurs it will be logged
  twice.

## JMS Serializer as Templating Engine

When returning an array or view model from your controller, JMS Serializer
can pick it up, when ``$request->getRequestFormat()`` returns `json` or `xml`.

This works in combination with view models and you can return them from your
controller and let JMS Serializer convert them correctly.
