<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

interface ControllerYieldApplier
{
    public function supports($yield);
    public function apply($yield, Request $request, Response $response);
}
