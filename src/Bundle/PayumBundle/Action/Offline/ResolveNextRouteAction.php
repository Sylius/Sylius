<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Action\Offline;

use Payum\Core\Action\ActionInterface;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Component\Payment\Model\PaymentInterface;

final class ResolveNextRouteAction implements ActionInterface
{
    /**
     * @param ResolveNextRoute $request
     */
    public function execute($request): void
    {
        $request->setRouteName('sylius_shop_order_thank_you');
    }

    public function supports($request): bool
    {
        return
            $request instanceof ResolveNextRoute &&
            $request->getFirstModel() instanceof PaymentInterface
        ;
    }
}
