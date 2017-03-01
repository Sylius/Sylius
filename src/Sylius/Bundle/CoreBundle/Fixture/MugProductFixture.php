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
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MugProductFixture extends AbstractFixture
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
        return 'mug_product';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options, SuiteInterface $suite)
    {
        $options = $this->optionsResolver->resolve($options);

        $this->taxonFixture->load(['custom' => [[
            'code' => 'category',
            'name' => 'Category',
            'children' => [
                [
                    'code' => 'mugs',
                    'name' => 'Mugs',
                ]
            ]
        ]]], $suite);

        $mugMaterials = ['Invisible porcelain', 'Banana skin', 'Porcelain', 'Centipede'];
        $this->productAttributeFixture->load(['custom' => [
            [
                'name' => 'Mug material',
                'code' => 'mug_material',
                'type' => SelectAttributeType::TYPE,
                'configuration' => [
                    'multiple' => false,
                    'choices' => $mugMaterials,
                ]
            ],
        ]], $suite);

        $this->productOptionFixture->load(['custom' => [
            [
                'name' => 'Mug type',
                'code' => 'mug_type',
                'values' => [
                    'mug_type_medium' => 'Medium mug',
                    'mug_type_double' => 'Double mug',
                    'mug_type_monster' => 'Monster mug',
                ],
            ],
        ]], $suite);

        $products = [];
        $productsNames = $this->getUniqueNames($options['amount']);
        for ($i = 0; $i < $options['amount']; ++$i) {
            $products[] = [
                'name' => sprintf('Mug "%s"', $productsNames[$i]),
                'code' => $this->faker->uuid,
                'main_taxon' => 'mugs',
                'taxons' => ['mugs'],
                'product_attributes' => [
                    'mug_material' => [$this->faker->randomKey($mugMaterials)],
                ],
                'product_options' => ['mug_type'],
                'images' => [
                    'main' => sprintf('%s/../Resources/fixtures/%s', __DIR__, 'mugs.jpg'),
                    'thumbnail' => sprintf('%s/../Resources/fixtures/%s', __DIR__, 'mugs.jpg'),
                ],
            ];
        }

        $this->productFixture->load(['custom' => $products], $suite);
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
