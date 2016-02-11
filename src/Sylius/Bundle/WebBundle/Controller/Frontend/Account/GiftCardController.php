<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Account;

use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Symfony\Component\HttpFoundation\Response;

class GiftCardController extends FOSRestController
{
    /**
     * List orders of the current user.
     *
     * @return Response
     */
    public function indexAction()
    {
        $orders = $this->getOrderRepository()->findWithCoupons([
            'type' => CouponInterface::TYPE_GIFT_CARD,
            'user' => $this->getUser(),
        ]);

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:giftcard.html.twig')
            ->setData([
                'orders' => $orders,
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * @return OrderRepositoryInterface
     */
    protected function getOrderRepository()
    {
        return $this->get('sylius.repository.order');
    }
}
