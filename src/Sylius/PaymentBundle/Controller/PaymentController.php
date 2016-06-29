<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\PaymentBundle\Controller;

use Sylius\ResourceBundle\Controller\ResourceController;
use Sylius\Payment\PaymentTransitions;

/**
 * Payment controller.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class PaymentController extends ResourceController
{
    protected $stateMachineGraph = PaymentTransitions::GRAPH;
}
