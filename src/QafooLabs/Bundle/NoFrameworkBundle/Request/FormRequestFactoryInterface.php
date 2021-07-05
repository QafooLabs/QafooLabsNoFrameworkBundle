<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Request;

use QafooLabs\MVC\FormRequest;

/**
 * Class SymfonyFormRequest
 *
 * @package App\Form
 */
interface FormRequestFactoryInterface
{
    public function createFormRequest(): FormRequest;
}
