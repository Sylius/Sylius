<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
     * @var ProductVariantPriceCalculatorInterface
     */
    private $productVariantOriginalPriceCalculator;

    /**
     * @param ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
     * @param ProductVariantPriceCalculatorInterface $productVariantOriginalPriceCalculator
     */
    public function __construct(
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator,
        ProductVariantPriceCalculatorInterface $productVariantOriginalPriceCalculator
    ) {
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
        $this->productVariantOriginalPriceCalculator = $productVariantOriginalPriceCalculator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function getPrice(ProductVariantInterface $productVariant, array $context): int
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
    public function getOriginalPrice(ProductVariantInterface $productVariant, array $context): ?int
    {
        Assert::keyExists($context, 'channel');

        return $this
            ->productVariantOriginalPriceCalculator
            ->calculate($productVariant, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_calculate_price';
    }
}
