<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ChannelBasedPriceHelper extends Helper implements ChannelBasedPriceHelperInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var ProductVariantPriceCalculatorInterface
     */
    private $productVariantPriceCalculator;

    /**
     * @param CartContextInterface $cartContext
     * @param ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
     */
    public function __construct(
        CartContextInterface $cartContext,
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
    ) {
        $this->cartContext = $cartContext;
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceForCurrentChannel(ProductVariantInterface $productVariant)
    {
        /** @var OrderInterface $currentCart */
        $currentCart = $this->cartContext->getCart();

        return $this
            ->productVariantPriceCalculator
            ->calculate($productVariant, ['channel' => $currentCart->getChannel()])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_channel_variant_price';
    }
}
