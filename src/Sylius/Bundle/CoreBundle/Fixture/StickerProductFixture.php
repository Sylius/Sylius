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
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class StickerProductFixture extends AbstractFixture
{
    /**
     * @var AbstractResourceFixture
     */
    private $taxonFixture;

    /**
     * @var AbstractResourceFixture
     */
    private $productAttributeFixture;

    /**
     * @var AbstractResourceFixture
     */
    private $productOptionFixture;

    /**
     * @var AbstractResourceFixture
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
     * @param AbstractResourceFixture $taxonFixture
     * @param AbstractResourceFixture $productAttributeFixture
     * @param AbstractResourceFixture $productOptionFixture
     * @param AbstractResourceFixture $productFixture
     */
    public function __construct(
        AbstractResourceFixture $taxonFixture,
        AbstractResourceFixture $productAttributeFixture,
        AbstractResourceFixture $productOptionFixture,
        AbstractResourceFixture $productFixture
    ) {
        $this->taxonFixture = $taxonFixture;
        $this->productAttributeFixture = $productAttributeFixture;
        $this->productOptionFixture = $productOptionFixture;
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

        $this->taxonFixture->load(['custom' => [[
            'code' => 'category',
            'name' => 'Category',
            'children' => [
                [
                    'code' => 'stickers',
                    'name' => 'Stickers',
                ]
            ]
        ]]]);

        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'Sticker paper', 'code' => 'sticker_paper', 'type' => TextAttributeType::TYPE],
            ['name' => 'Sticker resolution', 'code' => 'sticker_resolution', 'type' => TextAttributeType::TYPE],
        ]]);

        $this->productOptionFixture->load(['custom' => [
            [
                'name' => 'Sticker size',
                'code' => 'sticker_size',
                'values' => [
                    'sticker_size_3' => '3"',
                    'sticker_size_5' => '5"',
                    'sticker_size_7' => '7"',
                ],
            ],
        ]]);

        $products = [];
        $productsNames = $this->getUniqueNames($options['amount']);
        for ($i = 0; $i < $options['amount']; ++$i) {
            $products[] = [
                'name' => sprintf('Sticker "%s"', $productsNames[$i]),
                'code' => $this->faker->uuid,
                'main_taxon' => 'stickers',
                'taxons' => ['stickers'],
                'variant_selection_method' => ProductInterface::VARIANT_SELECTION_CHOICE,
                'product_attributes' => [
                    'sticker_paper' => sprintf('Paper from tree %s', $this->faker->randomElement(['Wung', 'Tanajno', 'Lemon-San', 'Me-Gusta'])),
                    'sticker_resolution' => $this->faker->randomElement(['JKM XD', '476DPI', 'FULL HD', '200DPI']),
                ],
                'product_options' => ['sticker_size'],
                'images' => [
                    [sprintf('%s/../Resources/fixtures/%s', __DIR__, 'stickers.jpg'), 'main'],
                    [sprintf('%s/../Resources/fixtures/%s', __DIR__, 'stickers.jpg'), 'thumbnail'],
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

    /**
     * @param int $amount
     *
     * @return string
     */
    private function getUniqueNames($amount)
    {
        $productsNames = [];

        for ($i = 0; $i < $amount; ++$i) {
            $name = $this->faker->word;
            while (in_array($name, $productsNames)) {
                $name = $this->faker->word;
            }
            $productsNames[] = $name;
        }

        return $productsNames;
    }
}
