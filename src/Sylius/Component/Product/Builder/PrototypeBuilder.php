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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Prototype builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PrototypeBuilder implements PrototypeBuilderInterface
{
    /**
     * Product attribute repository.
     *
     * @var RepositoryInterface
     */
    protected $attributeValueRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $attributeValueRepository
     */
    public function __construct(RepositoryInterface $attributeValueRepository)
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function build(PrototypeInterface $prototype, ProductInterface $product)
    {
        foreach ($prototype->getAttributes() as $attribute) {
            $attributeValue = $this->attributeValueRepository->createNew();
            $attributeValue->setAttribute($attribute);

            $product->addAttribute($attributeValue);
        }

        foreach ($prototype->getOptions() as $option) {
            $product->addOption($option);
        }
    }
}
