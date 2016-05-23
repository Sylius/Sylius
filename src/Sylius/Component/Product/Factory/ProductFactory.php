<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Factory;

use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductFactory implements ProductFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RepositoryInterface
     */
    private $archetypeRepository;

    /**
     * @var ArchetypeBuilderInterface
     */
    private $archetypeBuilder;

    /**
     * @var FactoryInterface
     */
    private $variantFactory;

    /**
     * @param FactoryInterface $factory
     * @param RepositoryInterface $archetypeRepository
     * @param ArchetypeBuilderInterface $archetypeBuilder
     * @param FactoryInterface $variantFactory
     */
    public function __construct(
        FactoryInterface $factory,
        RepositoryInterface $archetypeRepository,
        ArchetypeBuilderInterface $archetypeBuilder,
        FactoryInterface $variantFactory
    ) {
        $this->factory = $factory;
        $this->archetypeRepository = $archetypeRepository;
        $this->archetypeBuilder = $archetypeBuilder;
        $this->variantFactory = $variantFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createWithVariant()
    {
        $variant = $this->variantFactory->createNew();

        $product = $this->factory->createNew();
        $product->addVariant($variant);

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromArchetype($archetypeCode)
    {
        if (null === $archetype = $this->archetypeRepository->findOneBy(['code' => $archetypeCode])) {
            throw new \InvalidArgumentException(sprintf('Requested archetype does not exist with code "%s".', $archetypeCode));
        }

        $product = $this->createNew();
        $product->setArchetype($archetype);
        $this->archetypeBuilder->build($product);

        return $product;
    }
}
