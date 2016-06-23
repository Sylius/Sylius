<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class MugProductFixture extends AbstractFixture
{
    /**
     * @var TaxonFixture
     */
    private $taxonFixture;

    /**
     * @var RepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var ProductAttributeFixture
     */
    private $productAttributeFixture;

    /**
     * @var ProductOptionFixture
     */
    private $productOptionFixture;

    /**
     * @var ProductArchetypeFixture
     */
    private $productArchetypeFixture;

    /**
     * @var ProductFixture
     */
    private $productFixture;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param TaxonFixture $taxonFixture
     * @param RepositoryInterface $taxonRepository
     * @param ProductAttributeFixture $productAttributeFixture
     * @param ProductOptionFixture $productOptionFixture
     * @param ProductArchetypeFixture $productArchetypeFixture
     * @param ProductFixture $productFixture
     */
    public function __construct(
        TaxonFixture $taxonFixture,
        RepositoryInterface $taxonRepository,
        ProductAttributeFixture $productAttributeFixture,
        ProductOptionFixture $productOptionFixture,
        ProductArchetypeFixture $productArchetypeFixture,
        ProductFixture $productFixture
    ) {
        $this->taxonFixture = $taxonFixture;
        $this->taxonRepository = $taxonRepository;
        $this->productAttributeFixture = $productAttributeFixture;
        $this->productOptionFixture = $productOptionFixture;
        $this->productArchetypeFixture = $productArchetypeFixture;
        $this->productFixture = $productFixture;

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
    public function getName()
    {
        return 'mug_product';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        $options = $this->optionsResolver->resolve($options);

        $taxons = [];
        if (null === $this->taxonRepository->findOneBy(['code' => 'CATEGORY'])) {
            $taxons[] = ['name' => 'Category', 'code' => 'CATEGORY', 'parent' => null];
        }

        if (null === $this->taxonRepository->findOneBy(['code' => 'BRAND'])) {
            $taxons[] = ['name' => 'Brand', 'code' => 'BRAND', 'parent' => null];
        }

        $this->taxonFixture->load(['custom' => array_merge($taxons, [
            ['name' => 'Mugs', 'code' => 'MUGS', 'parent' => 'CATEGORY'],
            ['name' => 'Mugland', 'code' => 'MUGLAND', 'parent' => 'BRAND'],
        ])]);

        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'Mug material', 'code' => 'MUG-MATERIAL', 'type' => TextAttributeType::TYPE],
        ]]);

        $this->productOptionFixture->load(['custom' => [
            [
                'name' => 'Mug type',
                'code' => 'MUG-TYPE',
                'values' => [
                    'MUG-TYPE-MEDIUM' => 'Medium mug',
                    'MUG-TYPE-DOUBLE' => 'Double mug',
                    'MUG-TYPE-MONSTER' => 'Monster mug',
                ],
            ],
        ]]);

        $this->productArchetypeFixture->load(['custom' => [
            [
                'name' => 'Mug',
                'code' => 'MUG',
                'product_attributes' => ['MUG-MATERIAL'],
                'product_options' => ['MUG-TYPE'],
            ],
        ]]);

        $products = [];
        for ($i = 0; $i < $options['amount']; ++$i) {
            $products[] = [
                'name' => sprintf('Mug "%s"', $this->faker->word),
                'code' => $this->faker->uuid,
                'main_taxon' => 'MUGS',
                'product_archetype' => 'MUG',
                'taxons' => ['MUGS', 'MUGLAND'],
                'product_attributes' => [
                    'MUG-MATERIAL' => $this->faker->randomElement(['Invisible porcelain', 'Banana skin', 'Porcelain', 'Centipede']),
                ],
            ];
        }

        $this->productFixture->load(['custom' => $products]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNode
            ->children()
                ->integerNode('amount')->isRequired()->min(0)->end()
        ;
    }
}
