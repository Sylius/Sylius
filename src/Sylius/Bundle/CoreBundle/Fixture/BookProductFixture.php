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
use Sylius\Component\Attribute\AttributeType\IntegerAttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookProductFixture extends AbstractFixture
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
     * @param AbstractResourceFixture $productFixture
     * @param string $baseLocaleCode
     */
    public function __construct(
        AbstractResourceFixture $taxonFixture,
        AbstractResourceFixture $productAttributeFixture,
        AbstractResourceFixture $productFixture,
        string $baseLocaleCode
    ) {
        $this->taxonFixture = $taxonFixture;
        $this->productAttributeFixture = $productAttributeFixture;
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
        return 'book_product';
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
                    'code' => 'books',
                    'name' => 'Books',
                ],
            ],
        ]]]);

        $bookGenres = [
            $this->faker->uuid => [$this->baseLocaleCode => 'Science Fiction'],
            $this->faker->uuid => [$this->baseLocaleCode => 'Romance'],
            $this->faker->uuid => [$this->baseLocaleCode => 'Thriller'],
            $this->faker->uuid => [$this->baseLocaleCode => 'Sports'],
        ];
        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'Book author', 'code' => 'book_author', 'type' => TextAttributeType::TYPE],
            ['name' => 'Book ISBN', 'code' => 'book_isbn', 'type' => TextAttributeType::TYPE],
            ['name' => 'Book pages', 'code' => 'book_pages', 'type' => IntegerAttributeType::TYPE],
            [
                'name' => 'Book genre',
                'code' => 'book_genre',
                'type' => SelectAttributeType::TYPE,
                'configuration' => [
                    'multiple' => true,
                    'choices' => $bookGenres,
                ],
            ],
        ]]);

        $products = [];
        $productsNames = $this->getUniqueNames($options['amount']);
        for ($i = 0; $i < $options['amount']; ++$i) {
            $authorName = $this->faker->name;

            $products[] = [
                'name' => sprintf('Book "%s" by %s', $productsNames[$i], $authorName),
                'code' => $this->faker->uuid,
                'main_taxon' => 'books',
                'taxons' => ['books'],
                'product_attributes' => [
                    'book_author' => $authorName,
                    'book_isbn' => $this->faker->isbn13,
                    'book_pages' => $this->faker->numberBetween(42, 1024),
                    'book_genre' => $this->faker->randomElements(array_keys($bookGenres), $this->faker->numberBetween(1, count($bookGenres))),
                ],
                'images' => [
                    [
                        'path' => sprintf('%s/../Resources/fixtures/%s', __DIR__, 'books.jpg'),
                        'type' => 'main',
                    ],
                    [
                        'path' => sprintf('%s/../Resources/fixtures/%s', __DIR__, 'books.jpg'),
                        'type' => 'thumbnail',
                    ],
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
