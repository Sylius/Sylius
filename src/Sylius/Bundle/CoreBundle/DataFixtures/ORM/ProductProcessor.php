<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Nelmio\Alice\ProcessorInterface;
use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\VariableProductBundle\Generator\VariantGenerator;
use Symfony\Component\Intl\Intl;

/**
 * Product processor: generates all possible variants with random prices.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ProductProcessor implements ProcessorInterface
{
    private $variantGenerator;
    private $provider;

    public function __construct(VariantGenerator $variantGenerator, ProductProvider $provider)
    {
        $this->variantGenerator = $variantGenerator;
        $this->provider = $provider;
    }

    public function preProcess($product)
    {
        if ($product instanceof ProductInterface && $product->hasOptions()) {

            // Generates all possible variants with random prices.
            $this
                ->variantGenerator
                ->generate($product)
            ;

            foreach ($product->getVariants() as $variant) {
                $variant->setAvailableOn($this->provider->availableOn());
                $variant->setPrice($this->provider->price());
                $variant->setSku($this->provider->sku());
                $variant->setOnHand($this->provider->onHand());
            }
        }

        return;
    }

    public function postProcess($product)
    {
        return;
    }
}