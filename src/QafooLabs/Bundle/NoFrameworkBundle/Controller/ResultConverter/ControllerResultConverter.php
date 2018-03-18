<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

interface ControllerResultConverter
{
    public function supports($result);
    public function convert($result, Request $request, Response $response = null) : Response;
}
