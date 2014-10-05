<?php

namespace QafooLabs\MVC\Form;

use QafooLabs\MVC\FormRequest;

class ValidFormRequest implements FormRequest
{
    private $validData;

    public function __construct($validData)
    {
        $this->validData = $validData;
    }

    /**
     * Attempt to handle a form and return true when handled and data is valid.
     *
     * @param string|Typeinterface $formType
     * @param array|object $bindData
     * @param array $options
     * @throws Exception\FormAlreadyHandledException when a form was already bound on this request before.
     *
     * @return bool
     */
    public function handle($formType, $bindData = null, array $options = array())
    {
        return true;
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
        return $this->validData;
    }

    /**
     * Is the bound form valid?
     *
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Is the request bound to a form?
     *
     * @return bool
     */
    public function isBound()
    {
        return true;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        throw new \BadMethodCallException("Not supported in InvalidFormRequest");
    }

    /**
     * Create the form view for the handled form.
     *
     * Throws exception when no form was handled yet.
     *
     * @return \Symfony\Component\Form\FormViewInterface
     */
    public function createFormView()
    {
        return null;
    }
}
