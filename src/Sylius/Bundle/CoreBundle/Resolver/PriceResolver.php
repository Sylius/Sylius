<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Resolver;

use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Resolver\ItemResolverInterface;
use Sylius\Component\Cart\Resolver\ItemResolvingException;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\User\Model\CustomerAwareInterface;
use Sylius\Component\User\Model\GroupableInterface;

class PriceResolver implements ItemResolverInterface
{
    private $priceCalculator;

    public function __construct(DelegatingCalculatorInterface $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(CartItemInterface $item, $data, VariantInterface $variant = null)
    {
        if (!$variant instanceof PriceableInterface) {
            throw new ItemResolvingException('Requested product is not priceable.');
        }

        $order   = $item->getOrder();
        $context = array('quantity' => $item->getQuantity());

        if ($order instanceof CustomerAwareInterface) {
            $customer = $order->getCustomer();
            if ($customer && $customer instanceof GroupableInterface) {
                $context['groups'] = $customer->getGroups()->toArray();
            }
        }

        $item->setUnitPrice($this->priceCalculator->calculate($variant, $context));
    }
}
