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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Templating\Helper\Helper;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PriceHelper extends Helper
{
    /**
     * @var ProductVariantPriceCalculatorInterface
     */
    private $productVariantPriceCalculator;

    /**
     * @param ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
     */
    public function __construct(ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(ProductVariantInterface $productVariant, array $context)
    {
        Assert::keyExists($context, 'channel');

        return $this
            ->productVariantPriceCalculator
            ->calculate($productVariant, $context)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_calculate_price';
    }
}
