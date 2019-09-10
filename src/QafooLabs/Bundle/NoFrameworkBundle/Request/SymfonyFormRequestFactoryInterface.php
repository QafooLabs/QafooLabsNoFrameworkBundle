<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Request;

use QafooLabs\MVC\FormRequest;

/**
 * Class SymfonyFormRequest
 *
 * @package App\Form
 */
interface SymfonyFormRequestFactoryInterface
{
    public function createFormRequest(): FormRequest;
}
