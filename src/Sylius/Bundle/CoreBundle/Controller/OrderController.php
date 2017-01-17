<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\OrderBundle\Controller\OrderController as BaseOrderController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class OrderController extends BaseOrderController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function thankYouAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $orderId = $request->getSession()->get('sylius_order_id', null);

        if (null === $orderId) {
            $options = $configuration->getParameters()->get('after_failure');

            return $this->redirectHandler->redirectToRoute(
                $configuration,
                isset($options['route']) ? $options['route'] : 'sylius_shop_homepage',
                isset($options['parameters']) ? $options['parameters'] : []
            );
        }

        $request->getSession()->remove('sylius_order_id');
        $order = $this->repository->find($orderId);
        Assert::notNull($order);

        $view = View::create()
            ->setData([
                'order' => $order
            ])
            ->setTemplate($configuration->getParameters()->get('template'))
        ;

        return $this->viewHandler->handle($configuration, $view);
    }
}
