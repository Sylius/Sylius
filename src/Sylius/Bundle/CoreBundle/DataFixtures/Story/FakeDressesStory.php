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
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Zenstruck\Foundry\Story;

final class FakeDressesStory extends Story implements FakeDressesStoryInterface
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
                    'code' => 'dresses',
                    'translations' => [
                        'en_US' => ['name' => 'Dresses'],
                        'fr_FR' => ['name' => 'Robes'],
                    ],
                ],
            ])
            ->create()
        ;
    }

    private function createAttributes(): void
    {
        $this->productAttributeFactory::new()
            ->withCode('dress_brand')
            ->withName('Dress brand')
            ->withType(AttributeValueInterface::STORAGE_TEXT)
            ->create()
        ;

        $this->productAttributeFactory::new()
            ->withCode('dress_collection')
            ->withName('Dress collection')
            ->withType(AttributeValueInterface::STORAGE_TEXT)
            ->create()
        ;

        $this->productAttributeFactory::new()
            ->withCode('dress_material')
            ->withName('Dress material')
            ->withType(AttributeValueInterface::STORAGE_TEXT)
            ->create()
        ;

        $this->productAttributeFactory::new()
            ->withCode('length')
            ->withName('Length')
            ->withType(AttributeValueInterface::STORAGE_INTEGER)
            ->create()
        ;
    }

    private function createOptions(): void
    {
        $this->productOptionFactory::new()
            ->withCode('dress_size')
            ->withName('Dress size')
            ->withValues([
                'dress_s' => 'S',
                'dress_m' => 'M',
                'dress_l' => 'L',
                'dress_xl' => 'XL',
                'dress_xxl' => 'XXL',
            ])
            ->create()
        ;

        $this->productOptionFactory::new()
            ->withCode('dress_height')
            ->withName('Dress height')
            ->withValues([
                'dress_height_petite' => 'Petite',
                'dress_height_regular' => 'Regular',
                'dress_height_tall' => 'Tall',
            ])
            ->create()
        ;
    }

    private function createProducts(): void
    {
        $this->productFactory::new()
            ->withName('Beige strappy summer dress')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('dresses')
            ->withTaxa(['dresses'])
            ->withProductAttributes([
                'dress_brand'=> 'You are breathtaking',
                'dress_collection' => 'Sylius Winter 2019',
                'dress_material' => '100% polyester',
            ])
            ->withProductOptions([
                'dress_size',
                'dress_height',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/dresses/dress_01.jpg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('Off shoulder boho dress')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('dresses')
            ->withTaxa(['dresses'])
            ->withProductAttributes([
                'dress_brand'=> 'You are breathtaking',
                'dress_collection' => 'Sylius Winter 2019',
                'dress_material' => '100% wool',
            ])
            ->withProductOptions([
                'dress_size',
                'dress_height',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/dresses/dress_02.jpg', 'type' => 'main'],
            ])
            ->create()
        ;

        $this->productFactory::new()
            ->withName('Ruffle wrap festival dress')
            ->withTaxCategory('clothing')
            ->withChannels(['FASHION_WEB'])
            ->withMainTaxon('dresses')
            ->withTaxa(['dresses'])
            ->withProductAttributes([
                'dress_brand'=> 'You are breathtaking',
                'dress_collection' => 'Sylius Winter 2019',
                'dress_material' => '100% polyester',
                'length' => 100
            ])
            ->withProductOptions([
                'dress_size',
                'dress_height',
            ])
            ->withImages([
                ['path' => '@SyliusCoreBundle/Resources/fixtures/dresses/dress_03.jpg', 'type' => 'main'],
            ])
            ->create()
        ;
    }
}
