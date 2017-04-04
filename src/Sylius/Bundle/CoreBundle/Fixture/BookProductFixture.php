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
use Sylius\Component\Attribute\AttributeType\IntegerAttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
     */
    public function __construct(
        AbstractResourceFixture $taxonFixture,
        AbstractResourceFixture $productAttributeFixture,
        AbstractResourceFixture $productFixture
    ) {
        $this->taxonFixture = $taxonFixture;
        $this->productAttributeFixture = $productAttributeFixture;
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
        return 'book_product';
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
                    'code' => 'books',
                    'name' => 'Books',
                ]
            ]
        ]]]);

        $bookGenres = ['science_fiction' => 'Science Fiction', 'romance' => 'Romance', 'thriller' => 'Thriller', 'sports' => 'Sports'];
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
                ]
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
                    [sprintf('%s/../Resources/fixtures/%s', __DIR__, 'books.jpg'), 'main'],
                    [sprintf('%s/../Resources/fixtures/%s', __DIR__, 'books.jpg'), 'thumbnail'],
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
