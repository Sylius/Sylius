<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Action\Offline;

use Payum\Core\Action\ActionInterface;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Component\Payment\Model\PaymentInterface;

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
        $request->setRouteName('sylius_shop_order_thank_you');
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
