<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductAssociationExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $productAssociationFactory;

    /**
     * @var ProductAssociationTypeRepositoryInterface
     */
    private $productAssociationTypeRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $productAssociationFactory
     * @param ProductAssociationTypeRepositoryInterface $productAssociationTypeRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        FactoryInterface $productAssociationFactory,
        ProductAssociationTypeRepositoryInterface $productAssociationTypeRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productAssociationFactory = $productAssociationFactory;
        $this->productAssociationTypeRepository = $productAssociationTypeRepository;
        $this->productRepository = $productRepository;

        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ProductAssociationInterface $productAssociation */
        $productAssociation = $this->productAssociationFactory->createNew();
        $productAssociation->setType($options['type']);
        $productAssociation->setOwner($options['owner']);

        foreach ($options['associated_products'] as $associatedProduct) {
            $productAssociation->addAssociatedProduct($associatedProduct);
        }

        return $productAssociation;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('type', LazyOption::randomOne($this->productAssociationTypeRepository))
            ->setAllowedTypes('type', ['string', ProductAssociationTypeInterface::class])
            ->setNormalizer('type', LazyOption::findOneBy($this->productAssociationTypeRepository, 'code'))

            ->setDefault('owner', LazyOption::randomOne($this->productRepository))
            ->setAllowedTypes('owner', ['string', ProductInterface::class])
            ->setNormalizer('owner', LazyOption::findOneBy($this->productRepository, 'code'))

            ->setDefault('associated_products', LazyOption::randomOnes($this->productRepository, 3))
            ->setAllowedTypes('associated_products', 'array')
            ->setNormalizer('associated_products', LazyOption::findBy($this->productRepository, 'code'))
        ;
    }
}
