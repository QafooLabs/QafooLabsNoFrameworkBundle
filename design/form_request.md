# Handle forms with FormRequest

This is a draft for implementation.

Use of the Symfony form framework tends to complicate controllers, because of
the various methods you have to call in the right order. Request processing,
data mapping and validation all require a huge amount of service interaction
with forms. Consequently it is not easily possible to test controllers as a
unit when using the form framework.

Experiementation with various APIs in the past I finally found a solution
that seems workable.

By introducing an interface ``FormRequest`` with the following API:

```php
<?php
interface FormRequest
{
    /**
     * Attempt to handle a form and return true when handled and data is valid.
     *
     * Throws exception when a form was already bound on this request before.
     *
     * @return bool
     */
    public function handle($formName, $bindData = null, array $options = array());

    /**
     * Bind request to form. Same as handle except it already returns true when form is bound.
     *
     * @return bool
     */
    public function bind($formName, $bindData = null, array $options = array());

    /**
     * Use this to retrieve the validated data from the form even when you attached `$bindData`.
     *
     * Only by using this method you can mock the form handling by providing a replacement valid value in tests.
     *
     * @return mixed
     */
    public function getValidData();

    /**
     * Is the bound form valid?
     *
     * @return bool
     */
    public function isValid();

    /**
     * Is the request bound to a form?
     *
     * @return bool
     */
    public function isBound();

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm();

    /**
     * Create the form view for the handled form.
     *
     * Throws exception when no form was handled yet.
     *
     * @return \Symfony\Component\Form\FormViewInterface
     */
    public function createFormView();
}
```

With this API we can write a form action this way:

```php
<?php
public function editAction(FormRequest $formRequest, $id)
{
    $entity = $this->repository->find($id);

    if (!$formRequest->handle(new SomeFormType(), $entity)) {
        return array('form' => $formRequest->createFormView(), 'entity' => $entity);
    }

    $data = $formRequest->getValidData();

    // HERE BE CODE

    return $this->redirect();
}
```

Nortworthy Implementation Details:

- During a normal request QafooLabsFrameworkExtraBundle will create a Symfony
  specific instance that wraps the FormFactory and the original Symfony request
  instance using the ParamConverter API of the SensioFrameworkExtraBundle.

- For testing you can use one of `new ValidFormRequest($validData);`, `new
  InvalidFormRequest();` or `new NotBoundFormRequest();` to initialize the form
  request into any of these three states whenever `handle()` or `bind()` is
  called.

