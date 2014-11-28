# QafooLabs NoFrameworkBundle

*Disclaimer: This is not an official Qafoo product but a prototype. We don't provide support on this repository.*

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

## Installation

Add bundle to your application kernel:

```php
$bundles = array(
    // ...
    new QafooLabs\Bundle\NoFrameworkBundle\QafooLabsNoFrameworkBundle(),
);
```

Disable view listener in SensioFrameworkExtraBundle if you are using that (not a requirement anymore):

```yml
# app/config/config.yml
sensio_framework_extra:
    view:
        annotations: false
```

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

## Inject TokenContext into actions

In Symfony access to security related information is available through the
`security.context` service.  This is bad from a design perspective, because it
introduces a stateful service whenever access to security related information
is needed.

To avoid access to the security state from a service, it needs to be passed as
arguments, starting with the controller action.

That is what the `TokenContext` class is for. Just add a typehint for it to
any action and NoFrameworkBundle will pass this object into your action. From
it you have access to various security related methods:

```php
<?php
# src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

use QafooLabs\MVC\TokenContext;

class DefaultController
{
    public function redirectAction(TokenContext $context)
    {
        if ($context->hasToken()) {
            $user = $context->getCurrentUser();
        } else if ($context->hasAnonymousToken()) {
            // do anon stuff
        }

        if ($context->isGranted('ROLE_ADMIN')) {
            // do admin stuff
            echo $context->getCurrentUserId();
            echo $context->getCurrentUsername();
        }
    }
}
```

For Symfony a concrete implementation `SymfonyTokenContext` is used for the
interface that uses `security.context` internally.

In unit tests where you want to test the controller you can use the `MockTokenContext`
instead. It doesnt work with complex `isGranted()` checks or the token, but if you only
use the user object it allows very simple test setup.

## Working with FormRequest

Handling forms in Symfony typically leads to complicated, untestable controller actions
that are very tightly coupled to various Symfony services. To avoid having to deal with
`form.factory` inside a controller we introduced a specialized request object
that hides all this:

```php
<?php
# src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

use QafooLabs\MVC\FormRequest;

class ProductController
{
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository;
    }

    public function editAction(FormRequest $formRequest, $id)
    {
        $product = $this->repository->find($id);

        if (!$formRequest->handle(new ProductEditType(), $product)) {
            return array('form' => $formRequest->createFormView(), 'entity' => $product);
        }

        $product = $formRequest->getValidData();

        $this->repository->save($product);

        return new RedirectRouteResponse('Product.show', array('id' => $id));
    }
}
```

In tests you can use `new QafooLabs\MVC\Form\InvalidFormRequest()` and `new
QafooLabs\MVC\Form\ValidFormRequest($validData)` to work with forms in tests
for controllers.

## ParamConverter for Session

You can pass the session as an argument to a controller:

```
public function indexAction(Session $session)
{
}
```

## ParamConverter for Flash Messages

You can pass a flash object as an argument to a controller:

```
use QafooLabs\MVC\Flash;

public function indexAction(Flash $flash)
{
    $flash->add('notice', 'Hello World!');
}
```

## Helper for Controllers as Service

We added a ``controller_utils`` service that offers the functionality
of the Symfony base controller plus some extras.

See my blog post [Extending Symfony2: Controller Utils](http://www.whitewashing.de/2013/06/27/extending_symfony2__controller_utilities.html)
for reasoning.

## Convert Exceptions

Usually the libraries you are using or your own code throw exceptions that can be turned
into HTTP errors other than the 500 server error. To prevent having to do this in the controller
over and over again you can configure to convert those exceptions in a listener:

    qafoo_labs_no_framework:
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

In order for this to work, you need to add the JMS serializer bundle to your
application, and prepend the `jms` engine to the configured templating engines:

    framework:
        templating:
            engines: ['jms', 'twig']
