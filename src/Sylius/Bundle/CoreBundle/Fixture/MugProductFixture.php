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
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var string
     */
    private $baseLocaleCode;

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
     * @param string $baseLocaleCode
     */
    public function __construct(
        AbstractResourceFixture $taxonFixture,
        AbstractResourceFixture $productAttributeFixture,
        AbstractResourceFixture $productOptionFixture,
        AbstractResourceFixture $productFixture,
        string $baseLocaleCode
    ) {
        $this->taxonFixture = $taxonFixture;
        $this->productAttributeFixture = $productAttributeFixture;
        $this->productOptionFixture = $productOptionFixture;
        $this->productFixture = $productFixture;
        $this->baseLocaleCode = $baseLocaleCode;

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
        return 'mug_product';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $this->taxonFixture->load(['custom' => [[
            'code' => 'category',
            'name' => 'Category',
            'children' => [
                [
                    'code' => 'mugs',
                    'name' => 'Mugs',
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
                    [sprintf('%s/../Resources/fixtures/%s', __DIR__, 'mugs.jpg'), 'main'],
                    [sprintf('%s/../Resources/fixtures/%s', __DIR__, 'mugs.jpg'), 'thumbnail'],
                ],
            ];
        }

        $this->productFixture->load(['custom' => $products]);
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
     * @param int $amount
     *
     * @return array
     */
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
