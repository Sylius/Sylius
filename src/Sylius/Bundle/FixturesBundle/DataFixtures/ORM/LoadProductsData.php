<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * Default assortment products to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LoadProductsData extends DataFixture
{
    /**
     * Total variants created.
     *
     * @var int
     */
    protected $totalVariants = 0;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 30; ++$i) {
            switch (rand(0, 3)) {
                case 0:
                    $manager->persist($this->createTShirt($i));
                    break;

                case 1:
                    $manager->persist($this->createSticker($i));
                    break;

                case 2:
                    $manager->persist($this->createMug($i));
                    break;

                case 3:
                    $manager->persist($this->createBook($i));
                    break;
            }

            if (0 === $i % 10) {
                $manager->flush();
            }
        }

        $manager->flush();

        $this->defineTotalVariants();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 50;
    }

    /**
     * @param int $i
     *
     * @return ProductInterface
     */
    protected function createTShirt($i)
    {
        $product = $this->createProduct();

        $translatedNames = [
            $this->defaultLocale => sprintf('T-Shirt "%s"', $this->faker->word),
            'es_ES' => sprintf('Camiseta "%s"', $this->fakers['es_ES']->word),
        ];
        $this->addTranslatedFields($product, $translatedNames);
        $product->setCode($this->faker->uuid);

        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        $this->setTaxons($product, ['t-shirts', 'super_tees']);
        $product->setMainTaxon($this->getReference('Sylius.Taxon.t-shirts'));
        $product->setArchetype($this->getReference('Sylius.Archetype.t_shirt'));

        $this->addVariant($product);
        $this->setChannels($product, ['DEFAULT']);

        // T-Shirt brand.
        $randomBrand = $this->faker->randomElement(['Nike', 'Adidas', 'Puma', 'Potato']);
        $this->addAttribute($product, 't_shirt_brand', $randomBrand);

        // T-Shirt collection.
        $randomCollection = sprintf('Symfony2 %s %s', $this->faker->randomElement(['Summer', 'Winter', 'Spring', 'Autumn']), rand(1995, 2012));
        $this->addAttribute($product, 't_shirt_collection', $randomCollection);

        // T-Shirt material.
        $randomMaterial = $this->faker->randomElement(['Polyester', 'Wool', 'Polyester 10% / Wool 90%', 'Potato 100%']);
        $this->addAttribute($product, 't_shirt_material', $randomMaterial);

        $product->addOption($this->getReference('Sylius.Option.t_shirt_size'));
        $product->addOption($this->getReference('Sylius.Option.t_shirt_color'));

        $this->generateVariants($product);

        $this->setReference('Sylius.Product.'.$i, $product);

        return $product;
    }

    /**
     * Create sticker product.
     *
     * @param int $i
     *
     * @return ProductInterface
     */
    protected function createSticker($i)
    {
        $product = $this->createProduct();

        $translatedNames = [
            $this->defaultLocale => sprintf('Sticker "%s"', $this->faker->word),
            'es_ES' => sprintf('Pegatina "%s"', $this->fakers['es_ES']->word),
        ];
        $this->addTranslatedFields($product, $translatedNames);
        $product->setCode($this->faker->uuid);

        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        $this->setTaxons($product, ['stickers', 'stickypicky']);
        $product->setMainTaxon($this->getReference('Sylius.Taxon.stickers'));
        $product->setArchetype($this->getReference('Sylius.Archetype.sticker'));

        $this->addVariant($product);
        $this->setChannels($product, ['DEFAULT']);

        // Sticker resolution.
        $randomResolution = $this->faker->randomElement(['Waka waka', 'FULL HD', '300DPI', '200DPI']);
        $this->addAttribute($product, 'sticker_resolution', $randomResolution);

        // Sticker paper.
        $randomPaper = sprintf('Paper from tree %s', $this->faker->randomElement(['Wung', 'Yang', 'Lemon-San', 'Me-Gusta']));
        $this->addAttribute($product, 'sticker_paper', $randomPaper);

        $product->addOption($this->getReference('Sylius.Option.sticker_size'));

        $this->generateVariants($product);

        $this->setReference('Sylius.Product.'.$i, $product);

        return $product;
    }

    /**
     * Create mug product.
     *
     * @param int $i
     *
     * @return ProductInterface
     */
    protected function createMug($i)
    {
        $product = $this->createProduct();

        $translatedNames = [
            $this->defaultLocale => sprintf('Mug "%s"', $this->faker->word),
            'es_ES' => sprintf('Taza "%s"', $this->fakers['es_ES']->word),
        ];
        $this->addTranslatedFields($product, $translatedNames);
        $product->setCode($this->faker->uuid);

        $this->setTaxons($product, ['mugs', 'mugland']);
        $product->setMainTaxon($this->getReference('Sylius.Taxon.mugs'));
        $product->setArchetype($this->getReference('Sylius.Archetype.mug'));

        $this->addVariant($product);
        $this->setChannels($product, ['DEFAULT']);

        $randomMugMaterial = $this->faker->randomElement(['Invisible porcelain', 'Banana skin', 'Porcelain', 'Sand']);
        $this->addAttribute($product, 'mug_material', $randomMugMaterial);

        $product->addOption($this->getReference('Sylius.Option.mug_type'));

        $this->generateVariants($product);

        $this->setReference('Sylius.Product.'.$i, $product);

        return $product;
    }

    /**
     * Create book product.
     *
     * @param int $i
     *
     * @return ProductInterface
     */
    protected function createBook($i)
    {
        $product = $this->createProduct();

        $author = $this->faker->name;
        $isbn = $this->getUniqueISBN();

        $translatedNames = [
            $this->defaultLocale => sprintf('Book "%s" by "%s"', ucfirst($this->faker->word), $author),
            'es_ES' => sprintf('Libro "%s" de "%s"', ucfirst($this->fakers['es_ES']->word), $author),
        ];
        $this->addTranslatedFields($product, $translatedNames);
        $product->setCode($this->faker->uuid);

        $this->setTaxons($product, ['books', 'bookmania']);
        $product->setMainTaxon($this->getReference('Sylius.Taxon.books'));
        $product->setArchetype($this->getReference('Sylius.Archetype.book'));

        $this->addVariant($product, $isbn);
        $this->setChannels($product, ['DEFAULT']);

        $this->addAttribute($product, 'book_author', $author);
        $this->addAttribute($product, 'book_isbn', $isbn);
        $this->addAttribute($product, 'book_pages', $this->faker->randomNumber(3));

        $this->setReference('Sylius.Product.'.$i, $product);

        return $product;
    }

    /**
     * Generates all possible variants with random prices.
     *
     * @param ProductInterface $product
     */
    protected function generateVariants(ProductInterface $product)
    {
        $this
            ->getVariantGenerator()
            ->generate($product)
        ;

        foreach ($product->getVariants() as $variant) {
            $variant->setAvailableOn($this->faker->dateTimeThisYear);
            $variant->setPrice($this->faker->randomNumber(4));
            $variant->setCode($this->getUniqueCode());
            $variant->setOnHand($this->faker->randomNumber(1));

            $this->setReference('Sylius.Variant-'.$this->totalVariants, $variant);

            ++$this->totalVariants;
        }
    }

    /**
     * @param ProductInterface $product
     * @param string $code
     */
    protected function addVariant(ProductInterface $product, $code = null)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->get('sylius.factory.product_variant')->createNew();
        $variant->setProduct($product);
        $variant->setPrice($this->faker->randomNumber(4));
        $variant->setCode(null === $code ? $this->getUniqueCode() : $code);
        $variant->setAvailableOn($this->faker->dateTimeThisYear);
        $variant->setOnHand($this->faker->randomNumber(1));
        $variant->setTaxCategory($this->getTaxCategory('taxable'));

        $mainTaxon = $product->getMainTaxon();
        $image = clone $this->getReference('Sylius.Image.'.$mainTaxon->getCode());
        $variant->addImage($image);

        $product->addVariant($variant);

        $this->setReference('Sylius.Variant-'.$this->totalVariants, $variant);

        ++$this->totalVariants;
    }

    /**
     * Adds attribute to product with given value.
     *
     * @param ProductInterface $product
     * @param string $code
     * @param string $value
     */
    protected function addAttribute(ProductInterface $product, $code, $value)
    {
        /* @var $attribute AttributeValueInterface */
        $attribute = $this->getProductAttributeValueFactory()->createNew();
        $attribute->setAttribute($this->getReference('Sylius.Attribute.'.$code));
        $attribute->setProduct($product);
        $attribute->setValue($value);

        $product->addAttribute($attribute);
    }

    /**
     * Adds taxons to given product.
     *
     * @param ProductInterface $product
     * @param array $taxonCodes
     */
    protected function setTaxons(ProductInterface $product, array $taxonCodes)
    {
        $taxons = new ArrayCollection();

        foreach ($taxonCodes as $taxonCode) {
            $taxons->add($this->getReference('Sylius.Taxon.'.$taxonCode));
        }

        $product->setTaxons($taxons);
    }

    /**
     * Set channels.
     *
     * @param ProductInterface $product
     * @param array $channelCodes
     */
    protected function setChannels(ProductInterface $product, array $channelCodes)
    {
        foreach ($channelCodes as $code) {
            $product->addChannel($this->getReference('Sylius.Channel.'.$code));
        }
    }

    /**
     * @param string $code
     *
     * @return TaxCategoryInterface
     */
    protected function getTaxCategory($code)
    {
        return $this->getReference('Sylius.TaxCategory.'.$code);
    }

    /**
     * Get unique SKU.
     *
     * @return string
     */
    protected function getUniqueCode()
    {
        return $this->faker->unique()->uuid();
    }

    /**
     * Get unique ISBN number.
     *
     * @return string
     */
    protected function getUniqueISBN()
    {
        return $this->faker->unique()->uuid();
    }

    /**
     * @return ProductInterface
     */
    protected function createProduct()
    {
        return $this->getProductFactory()->createNew();
    }

    /**
     * Define constant with number of total variants created.
     */
    protected function defineTotalVariants()
    {
        define('SYLIUS_FIXTURES_TOTAL_VARIANTS', $this->totalVariants);
    }

    protected function addTranslatedFields(ProductInterface $product, $translatedNames)
    {
        foreach ($translatedNames as $locale => $name) {
            $product->setCurrentLocale($locale);
            $product->setFallbackLocale($locale);

            $product->setName($name);
            $product->setDescription($this->fakers[$locale]->paragraph);
            $product->setShortDescription($this->fakers[$locale]->sentence);
            $product->setMetaKeywords(str_replace(' ', ', ', $this->fakers[$locale]->sentence));
            $product->setMetaDescription($this->fakers[$locale]->sentence);
        }

        $product->setCurrentLocale($this->defaultLocale);
    }
}
