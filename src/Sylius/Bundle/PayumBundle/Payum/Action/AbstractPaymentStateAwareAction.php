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

use Finite\Factory\FactoryInterface;
use Payum\Core\Action\PaymentAwareAction;

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

    protected function updatePaymentState($payment, $previousState, $nextState)
    {
        $stateMachine = $this->factory->get($payment, 'sylius_payment');

        if (null !== $transition = $stateMachine->getTransitionToState($nextState)) {
            $stateMachine->apply($transition);
        }
    }
}
