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

<<<<<<< HEAD:src/Sylius/Bundle/ProductBundle/Builder/PrototypeBuilder.php
use Sylius\Bundle\ProductBundle\Model\ProductInterface;
use Sylius\Bundle\ProductBundle\Model\PrototypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
=======
use Sylius\Component\Product\Builder\PrototypeBuilderInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\PrototypeInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
>>>>>>> Creating Product component:src/Sylius/Component/Product/Builder/PrototypeBuilder.php

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
