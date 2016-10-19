<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Action\ActionInterface;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ResolveNextRouteAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param ResolveNextRoute $request
     */
    public function execute($request)
    {
        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();

        if ($payment->getState() === PaymentInterface::STATE_COMPLETED) {
            $request->setRouteName(
                'sylius_shop_order_thank_you'
            );

            return;
        }

        $request->setRouteName('sylius_shop_order_show');
        $request->setRouteParameters(['tokenValue' => $payment->getOrder()->getTokenValue()]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof ResolveNextRoute &&
            $request->getFirstModel() instanceof PaymentInterface
        ;
    }
}
