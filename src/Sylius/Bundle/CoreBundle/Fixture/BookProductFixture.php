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
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class BookProductFixture extends AbstractFixture
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
        return 'book_product';
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
            ['name' => 'Books', 'code' => 'BOOKS', 'parent' => 'CATEGORY'],
            ['name' => 'BookMania', 'code' => 'BOOKMANIA', 'parent' => 'BRAND'],
        ])]);

        $this->productAttributeFixture->load(['custom' => [
            ['name' => 'Book author', 'code' => 'BOOK-AUTHOR', 'type' => TextAttributeType::TYPE],
            ['name' => 'Book ISBN', 'code' => 'BOOK-ISBN', 'type' => TextAttributeType::TYPE],
            ['name' => 'Book pages', 'code' => 'BOOK-PAGES', 'type' => IntegerAttributeType::TYPE],
        ]]);

        $this->productArchetypeFixture->load(['custom' => [
            [
                'name' => 'Book',
                'code' => 'BOOK',
                'product_attributes' => ['BOOK-AUTHOR', 'BOOK-ISBN', 'BOOK-PAGES'],
                'product_options' => [],
            ],
        ]]);

        $products = [];
        for ($i = 0; $i < $options['amount']; ++$i) {
            $name = $this->faker->name;

            $products[] = [
                'name' => sprintf('Book "%s" by %s', $this->faker->word, $name),
                'code' => $this->faker->uuid,
                'main_taxon' => 'BOOKS',
                'product_archetype' => 'BOOK',
                'taxons' => ['BOOKS', 'BOOKMANIA'],
                'product_attributes' => [
                    'BOOK-AUTHOR' => $name,
                    'BOOK-ISBN' => $this->faker->isbn13,
                    'BOOK-PAGES' => $this->faker->numberBetween(42, 1024),
                ],
                'images' => [sprintf('%s/../Resources/fixtures/%s', __DIR__, 'books.jpg')],
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
