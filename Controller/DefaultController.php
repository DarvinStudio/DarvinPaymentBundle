<?php

namespace Darvin\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DarvinPaymentBundle:Default:index.html.twig');
    }
}
