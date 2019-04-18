<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Bundle\AdminBundle\Templating\Helper\VariantPriceHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class VariantPriceExtension extends AbstractExtension
{
    /** @var VariantPriceHelper */
    private $variantPriceHelper;

    public function __construct(VariantPriceHelper $variantPriceHelper)
    {
        $this->variantPriceHelper = $variantPriceHelper;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('sylius_variant_price', [$this->variantPriceHelper, 'getPriceWithCurrency']),
        ];
    }
}
