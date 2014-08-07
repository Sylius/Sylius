<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Builder;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\PrototypeInterface;
use Sylius\Component\Resource\Manager\DomainManagerInterface;

/**
 * Prototype builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PrototypeBuilder implements PrototypeBuilderInterface
{
    /**
     * Product attribute value manager.
     *
     * @var DomainManagerInterface
     */
    protected $attributeValueManager;

    /**
     * Constructor.
     *
     * @param DomainManagerInterface $attributeValueManager
     */
    public function __construct(DomainManagerInterface $attributeValueManager)
    {
        $this->attributeValueManager = $attributeValueManager;
    }

    /**
     * {@inheritdoc}
     */
    public function build(PrototypeInterface $prototype, ProductInterface $product)
    {
        foreach ($prototype->getAttributes() as $attribute) {
            $attributeValue = $this->attributeValueManager->createNew();
            $attributeValue->setAttribute($attribute);

            $product->addAttribute($attributeValue);
        }

        foreach ($prototype->getOptions() as $option) {
            $product->addOption($option);
        }
    }
}
