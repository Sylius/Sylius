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

use Faker\Factory;
use Faker\Generator;

@trigger_error('The "MugProductFixture" class is deprecated since Sylius 1.5 Use new product fixtures class located at "src/Sylius/Bundle/CoreBundle/Fixture/" instead.', \E_USER_DEPRECATED);

use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MugProductFixture extends AbstractFixture
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private AbstractResourceFixture $taxonFixture,
        private AbstractResourceFixture $productAttributeFixture,
        private AbstractResourceFixture $productOptionFixture,
        private AbstractResourceFixture $productFixture,
        private string $baseLocaleCode,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setRequired('amount')
                ->setAllowedTypes('amount', 'int')
        ;
    }

    public function getName(): string
    {
        return 'mug_product';
    }

    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $this->taxonFixture->load(['custom' => [[
            'code' => 'category',
            'name' => 'Category',
            'children' => [
                [
                    'code' => 'mugs',
                    'translations' => [
                        'en_US' => [
                            'name' => 'Mugs',
                        ],
                        'fr_FR' => [
                            'name' => 'Tasses',
                        ],
                    ],
                ],
            ],
        ]]]);

        $mugMaterials = [
            $this->faker->uuid => [$this->baseLocaleCode => 'Invisible porcelain'],
            $this->faker->uuid => [$this->baseLocaleCode => 'Banana skin'],
            $this->faker->uuid => [$this->baseLocaleCode => 'Porcelain'],
            $this->faker->uuid => [$this->baseLocaleCode => 'Centipede'],
        ];
        $this->productAttributeFixture->load(['custom' => [
            [
                'name' => 'Mug material',
                'code' => 'mug_material',
                'type' => SelectAttributeType::TYPE,
                'configuration' => [
                    'multiple' => false,
                    'choices' => $mugMaterials,
                ],
            ],
        ]]);

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
        ]]);

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
                    [
                        'path' => sprintf('%s/../Resources/fixtures/%s', __DIR__, 'mugs.jpg'),
                        'type' => 'main',
                    ],
                    [
                        'path' => sprintf('%s/../Resources/fixtures/%s', __DIR__, 'mugs.jpg'),
                        'type' => 'thumbnail',
                    ],
                ],
            ];
        }

        $this->productFixture->load(['custom' => $products]);
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('amount')->isRequired()->min(0)->end()
        ;
    }

    private function getUniqueNames(int $amount): array
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
