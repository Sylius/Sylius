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
final class StickerProductFixture extends AbstractFixture
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
        return 'sticker_product';
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
            ['name' => 'Stickers', 'code' => 'STICKERS', 'parent' => 'CATEGORY'],
            ['name' => 'StickyPicky', 'code' => 'STICKYPICKY', 'parent' => 'BRAND'],
        ])]);

        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'Sticker paper', 'code' => 'STICKER-PAPER', 'type' => TextAttributeType::TYPE],
            ['name' => 'Sticker resolution', 'code' => 'STICKER-RESOLUTION', 'type' => TextAttributeType::TYPE],
        ]]);

        $this->productOptionFixture->load(['custom' => [
            [
                'name' => 'Sticker SIZE',
                'code' => 'STICKER-SIZE',
                'values' => [
                    'STICKER-SIZE-3' => '3"',
                    'STICKER-SIZE-5' => '5"',
                    'STICKER-SIZE-7' => '7"',
                ],
            ],
        ]]);

        $this->productArchetypeFixture->load(['custom' => [
            [
                'name' => 'Sticker',
                'code' => 'STICKER',
                'product_attributes' => ['STICKER-PAPER', 'STICKER-RESOLUTION'],
                'product_options' => ['STICKER-SIZE'],
            ],
        ]]);

        $products = [];
        for ($i = 0; $i < $options['amount']; ++$i) {
            $products[] = [
                'name' => sprintf('Sticker "%s"', $this->faker->word),
                'code' => $this->faker->uuid,
                'main_taxon' => 'STICKERS',
                'product_archetype' => 'STICKER',
                'taxons' => ['STICKERS', 'STICKYPICKY'],
                'product_attributes' => [
                    'STICKER-PAPER' => sprintf('Paper from tree %s', $this->faker->randomElement(['Wung', 'Tanajno', 'Lemon-San', 'Me-Gusta'])),
                    'STICKER-RESOLUTION' => $this->faker->randomElement(['JKM XD', '476DPI', 'FULL HD', '200DPI']),
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
