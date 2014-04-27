# Framework Context

Context should be made explicit by using a *ContextObject* passed as parameter
into methods. Symfony however passes context around through services such as
`security.context` or `request` by default. Consequently controller code is
very hard to test.  Inside Twig however this context object exists with the
magic `app` variable.

We propose to introduce a `FrameworkContext` object that can be injected
into controllers using the ParamConverter mechanism:

```php
<?php
# src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

class DefaultController
{
    public function helloAction(FrameworkContext $context)
    {
        retun array('name' => $context->getCurrentUsername());
    }
}
```

The interface for this context is: 

```php
<?php
interface FrameworkContext
{
    /**
     * If a security context and token exists, retrieve the username.
     * 
     * Throws AccessDeniedHttpException when no valid token exists.
     *
     * @return string
     */
    public function getCurrentUsername();

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getCurrentUser();

    public function getToken();

    /**
     * @return bool
     */
    public function isGranted($role, $attributes = null);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @return string
     */
    public function getEnvironment();

    /**
     * @return bool
     */
    public function isDebug();
}
```

For tests you can create a mock version of the context that can be easily
configured to contain user-data, authorization rules and various data.
