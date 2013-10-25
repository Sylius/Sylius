<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PurchaseStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        throw new HttpException(501, 'The default purchase step has to be overwritten by one from SyliusPayumBundle. You have to implement this step if you do not use the bundle.');
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $this->displayAction($context);
    }
}
