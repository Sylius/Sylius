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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimilarProductAssociationFixture extends AbstractFixture
{
    /** @var AbstractResourceFixture */
    private $productAssociationTypeFixture;

    /** @var AbstractResourceFixture */
    private $productAssociationFixture;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        AbstractResourceFixture $productAssociationTypeFixture,
        AbstractResourceFixture $productAssociationFixture,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productAssociationTypeFixture = $productAssociationTypeFixture;
        $this->productAssociationFixture = $productAssociationFixture;
        $this->productRepository = $productRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setRequired('amount')
                ->setAllowedTypes('amount', 'int')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'similar_product_association';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $this->productAssociationTypeFixture->load(['custom' => [[
            'code' => 'similar_products',
            'name' => 'Similar products',
        ]]]);

        $products = $this->productRepository->findAll();
        $products = $this->faker->randomElements($products, $options['amount']);

        $productAssociations = [];
        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $productAssociations[] = [
                'type' => 'similar_products',
                'owner' => $product->getCode(),
                'associated_products' => $this->getAssociatedProductsAsArray($product),
            ];
        }

        $this->productAssociationFixture->load(['custom' => $productAssociations]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('amount')->isRequired()->min(0)->end()
        ;
    }

    /**
     * @return array|string[]
     */
    private function getAssociatedProductsAsArray(ProductInterface $owner): array
    {
        $products = $this->productRepository->findBy(['mainTaxon' => $owner->getMainTaxon()]);
        $products = $this->faker->randomElements($products, 2);

        $associatedProducts = [];
        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $associatedProducts[] = $product->getCode();
        }

        return $associatedProducts;
    }
}
