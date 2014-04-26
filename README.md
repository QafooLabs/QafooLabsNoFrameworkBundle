# QafooLabs FrameworkExtraBundle

**Note** This bundle is experimental. Use at own risk.

Symfony2 Bundle that improves Framework integration when working with the
following patterns/features:

1. Returning View data from controllers
2. Helper for Controllers as Service
3. JMS Serializer as Templating Engine
4. Convert Exceptions from Domain/Library Types to Framework Types

## Returning View data from controllers

This bundle replicates the ``@Extra\Template()`` annotation support
from the Sensio FrameworkExtraBundle, without requiring to add the annotation
to the controller actions.

You can just return arrays from controllers and the template names will
be inferred from Controller+Action-Method names.

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

You can optionally extend from ``QafooLabs\Bundle\FrameworkExtraBundle\View\ViewStruct``.
Every ``ViewStruct`` implementation has a constructor accepting and setting
key-value pairs of properties that exist on the view model class.

## Helper for Controllers as Service

We added a ``controller_utils`` service that offers the functionality
of the Symfony base controller plus some extras.

## JMS Serializer as Templating Engine

When returning an array or view model from your controller, JMS Serializer
can pick it up, when ``$request->getRequestFormat()`` returns `json` or `xml`.

## Convert Exceptions

Usually the libraries you are using or your own code throw exceptions that can be turned
into HTTP errors other than the 500 server error. To prevent having to do this in the controller
over and over again you can configure to convert those exceptions in a listener:

    qafoo_labs_framework_extra:
        convert_exceptions:
            Doctrine\ORM\EntityNotFoundException: Symfony\Component\HttpKernel\Exception\NotFoundHttpException

If you don't define conversions the listener is not registered. If an exception is converted
the original exception will specifically logged before conversion. That means when an exception
occurs it will be logged twice.
