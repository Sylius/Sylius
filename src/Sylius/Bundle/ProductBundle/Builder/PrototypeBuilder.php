<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Builder;

use Sylius\Bundle\ProductBundle\Model\ProductInterface;
use Sylius\Bundle\ProductBundle\Model\PrototypeInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Prototype builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PrototypeBuilder implements PrototypeBuilderInterface
{
    /**
     * Product property repository.
     *
     * @var RepositoryInterface
     */
    protected $productPropertyRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $productPropertyRepository
     */
    public function __construct(RepositoryInterface $productPropertyRepository)
    {
        $this->productPropertyRepository = $productPropertyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function build(PrototypeInterface $prototype, ProductInterface $product)
    {
        foreach ($prototype->getProperties() as $property) {
            $productProperty = $this->productPropertyRepository->createNew();
            $productProperty->setProperty($property);

            $product->addProperty($productProperty);
        }
    }
}
