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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAttributeFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductOptionFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Zenstruck\Foundry\Story;

final class FakeJeansStory extends Story implements FakeJeansStoryInterface
{
    public function __construct(
        private TaxonFactoryInterface $taxonFactory,
        private ProductAttributeFactoryInterface $productAttributeFactory,
        private ProductOptionFactoryInterface $productOptionFactory,
        private ProductFactoryInterface $productFactory,
    ) {
    }

    public function build(): void
    {
        $this->createTaxa();
        $this->createAttributes();
        $this->createOptions();
        $this->createProducts();
    }

    private function createTaxa(): void
    {
        $this->taxonFactory::new()
            ->withCode('MENU_CATEGORY')
            ->withName('Category')
            ->withTranslations([
                'en_US' => ['name' => 'Category'],
                'fr_FR' => ['name' => 'Catégorie'],
            ])
            ->withChildren([
                [
                    'code' => 'jeans',
                    'name' => 'Jeans',
                    'translations' => [
                        'en_US' => ['name' => 'Jeans'],
                        'fr_FR' => ['name' => 'Jeans'],
                    ],
                    'children' => [
                        [
                            'code' => 'men_jeans',
                            'translations' => [
                                'en_US' => [
                                    'name' => 'Men',
                                    'slug' => 'jeans/men',
                                ],
                                'fr_FR' => [
                                    'name' => 'Hommes',
                                    'slug' => 'jeans/hommes',
                                ],
                            ],
                        ],
                        [
                            'code' => 'women_jeans',
                            'translations' => [
                                'en_US' => [
                                    'name' => 'Women',
                                    'slug' => 'jeans/women',
                                ],
                                'fr_FR' => [
                                    'name' => 'Femmes',
                                    'slug' => 'jeans/femmes',
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->create()
        ;
    }

    private function createAttributes(): void
    {
        $this->productAttributeFactory::new()
            ->withCode('jeans_brand')
            ->withName('Jeans brand')
            ->withType(TextAttributeType::TYPE)
            ->create()
        ;

        $this->productAttributeFactory::new()
            ->withCode('jeans_collection')
            ->withName('Jeans collection')
            ->withType(TextAttributeType::TYPE)
            ->create()
        ;

        $this->productAttributeFactory::new()
            ->withCode('jeans_material')
            ->withName('Jeans material')
            ->withType(TextAttributeType::TYPE)
            ->create()
        ;
    }

    private function createOptions(): void
    {
        $this->productOptionFactory::new()
            ->withCode('jeans_size')
            ->withName('Jeans size')
            ->withValues([
                'jeans_size_s' => 'S',
                'jeans_size_m' => 'M',
                'jeans_size_l' => 'L',
                'jeans_size_xl' => 'XL',
                'jeans_size_xxl' => 'XXL',
            ])
            ->create()
        ;
    }

    private function createProducts(): void
    {
        $year = date('Y');

        $this->productFactory::new()
            ->withName('911M regular fit jeans')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('mens_jeans')
            ->withTaxa(['jeans', 'men_jeans'])
            ->withProductAttributes([
                'jeans_brand'=> 'You are breathtaking',
                'jeans_collection' => 'Sylius Winter '.$year,
                'jeans_material' => '100% jeans',
            ])
            ->withProductOptions([
                'jeans_size',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/man/jeans_01.jpg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('330M slim fit jeans')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('mens_jeans')
            ->withTaxa(['jeans', 'men_jeans'])
            ->withProductAttributes([
                'jeans_brand'=> 'Modern Wear',
                'jeans_collection' => 'Sylius Winter '.$year,
                'jeans_material' => '100% jeans',
            ])
            ->withProductOptions([
                'jeans_size',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/man/jeans_02.jpg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('990M regular fit jeans')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('mens_jeans')
            ->withTaxa(['jeans', 'men_jeans'])
            ->withProductAttributes([
                'jeans_brand'=> 'Celsius Small',
                'jeans_collection' => 'Sylius Winter '.$year,
                'jeans_material' => '100% jeans',
            ])
            ->withProductOptions([
                'jeans_size',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/man/jeans_03.jpg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('007M black elegance jeans')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('mens_jeans')
            ->withTaxa(['jeans', 'men_jeans'])
            ->withProductAttributes([
                'jeans_brand'=> 'Date & Banana',
                'jeans_collection' => 'Sylius Winter '.$year,
                'jeans_material' => '100% jeans',
            ])
            ->withProductOptions([
                'jeans_size',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/man/jeans_04.svg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('727F patched cropped jeans')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('women_jeans')
            ->withTaxa(['jeans', 'women_jeans'])
            ->withProductAttributes([
                'jeans_brand'=> 'You are breathtaking',
                'jeans_collection' => 'Sylius Winter '.$year,
                'jeans_material' => '100% jeans',
            ])
            ->withProductOptions([
                'jeans_size',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/woman/jeans_01.jpg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('111F patched jeans with fancy badges')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('women_jeans')
            ->withTaxa(['jeans', 'women_jeans'])
            ->withProductAttributes([
                'jeans_brand'=> 'You are breathtaking',
                'jeans_collection' => 'Sylius Winter '.$year,
                'jeans_material' => '100% jeans',
            ])
            ->withProductOptions([
                'jeans_size',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/woman/jeans_02.jpg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('000F office grey jeans')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('women_jeans')
            ->withTaxa(['jeans', 'women_jeans'])
            ->withProductAttributes([
                'jeans_brand'=> 'Modern Wear',
                'jeans_collection' => 'Sylius Winter '.$year,
                'jeans_material' => '100% jeans',
            ])
            ->withProductOptions([
                'jeans_size',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/woman/jeans_03.jpg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('666F boyfriend jeans with rips')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('women_jeans')
            ->withTaxa(['jeans', 'women_jeans'])
            ->withProductAttributes([
                'jeans_brand'=> 'Modern Wear',
                'jeans_collection' => 'Sylius Winter '.$year,
                'jeans_material' => '100% jeans',
            ])
            ->withProductOptions([
                'jeans_size',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/woman/jeans_04.jpg', 'type' => 'main'],
            ])
            ->create()
        ;
    }
}
