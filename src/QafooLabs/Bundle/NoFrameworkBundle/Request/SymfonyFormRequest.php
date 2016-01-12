<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Request;

use QafooLabs\MVC\Exception;
use QafooLabs\MVC\FormRequest;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

class SymfonyFormRequest implements FormRequest
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface;
     */
    private $formFactory;

    /**
     * @var \Symfony\Component\Form\FormInterface;
     */
    private $form;

    public function __construct(Request $request, FormFactoryInterface $formFactory)
    {
        $this->request = $request;
        $this->formFactory = $formFactory;
    }

    /**
     * Attempt to handle a form and return true when handled and data is valid.
     *
     * @param string|FormTypeInterface $formType
     * @param array|object $bindData
     * @param array $options
     * @throws Exception\FormAlreadyHandledException when a form was already bound on this request before.
     *
     * @return bool
     */
    public function handle($formType, $bindData = null, array $options = array())
    {
        if ($this->form !== null) {
            throw new Exception\FormAlreadyHandledException($this->form->getName());
        }

        $this->form = $this->formFactory->create($formType, $bindData, $options);
        $this->form->handleRequest($this->request);

        return $this->form->isSubmitted() && $this->form->isValid();
    }

    /**
     * Use this to retrieve the validated data from the form even when you attached `$bindData`.
     *
     * Only by using this method you can mock the form handling by providing a replacement valid value in tests.
     *
     * @return mixed
     */
    public function getValidData()
    {
        $this->assertFormHandled();

        return $this->form->getData();
    }

    /**
     * Is the bound form valid?
     *
     * @return bool
     */
    public function isValid()
    {
        $this->assertFormHandled();

        return $this->form->isValid();
    }

    /**
     * Is the request bound to a form?
     *
     * @return bool
     */
    public function isBound()
    {
        $this->assertFormHandled();

        return $this->form->isSubmitted();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        $this->assertFormHandled();

        return $this->form;
    }

    /**
     * Create the form view for the handled form.
     *
     * Throws exception when no form was handled yet.
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function createFormView()
    {
        $this->assertFormHandled();

        return $this->form->createView();
    }

    private function assertFormHandled()
    {
        if ($this->form === null) {
            throw new Exception\NoFormHandledException();
        }
    }
}
