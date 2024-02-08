<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAssociationExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private OptionsResolver $optionsResolver;

    public function __construct(
        private FactoryInterface $productAssociationFactory,
        private ProductAssociationTypeRepositoryInterface $productAssociationTypeRepository,
        private ProductRepositoryInterface $productRepository,
    ) {
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ProductAssociationInterface
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

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', LazyOption::randomOne($this->productAssociationTypeRepository))
            ->setAllowedTypes('type', ['string', ProductAssociationTypeInterface::class])
            ->setNormalizer('type', LazyOption::getOneBy($this->productAssociationTypeRepository, 'code'))

            ->setDefault('owner', LazyOption::randomOne($this->productRepository))
            ->setAllowedTypes('owner', ['string', ProductInterface::class])
            ->setNormalizer('owner', LazyOption::getOneBy($this->productRepository, 'code'))

            ->setDefault('associated_products', LazyOption::randomOnes($this->productRepository, 3))
            ->setAllowedTypes('associated_products', 'array')
            ->setNormalizer('associated_products', LazyOption::findBy($this->productRepository, 'code'))
        ;
    }
}
