<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Request;

use QafooLabs\MVC\FormRequest;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class SymfonyFormRequestFactory to creates a SymfonyFormRequest via the Symfony request_stack.
 */
final class SymfonyFormRequestFactory implements FormRequestFactoryInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    public function __construct(RequestStack $requestStack, FormFactoryInterface $formFactory)
    {
        $this->requestStack = $requestStack;
        $this->formFactory  = $formFactory;
    }

    public function createFormRequest(): FormRequest
    {
        return new SymfonyFormRequest($this->requestStack->getCurrentRequest(), $this->formFactory);
    }
}
