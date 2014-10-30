<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Action;

use Payum\Core\Action\PaymentAwareAction;
use SM\Factory\FactoryInterface;
use Sylius\Component\Payment\PaymentTransitions;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
abstract class AbstractPaymentStateAwareAction extends PaymentAwareAction
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    protected function updatePaymentState($payment, $nextState)
    {
        $stateMachine = $this->factory->get($payment, PaymentTransitions::GRAPH);

        if (null !== $transition = $stateMachine->getTransitionToState($nextState)) {
            $stateMachine->apply($transition);
        }
    }
}
