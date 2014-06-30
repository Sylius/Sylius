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
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * Default assortment products to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadProductsData extends DataFixture
{
    /**
     * Total variants created.
     *
     * @var integer
     */
    private $totalVariants = 0;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // T-Shirts...
        for ($i = 1; $i <= 120; $i++) {
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

            if (0 === $i % 20) {
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
        return 6;
    }

    /**
     * Creates t-shirt product.
     *
     * @param integer $i
     *
     * @return ProductInterface
     */
    protected function createTShirt($i)
    {
        $product = $this->createProduct();
        $product->setTaxCategory($this->getTaxCategory('Taxable goods'));
        $product->setName(sprintf('T-Shirt "%s"', $this->faker->word));
        $product->setDescription($this->faker->paragraph);
        $product->setShortDescription($this->faker->sentence);
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        $this->addMasterVariant($product);

        $this->setTaxons($product, array('T-Shirts', 'SuperTees'));

        // T-Shirt brand.
        $randomBrand = $this->faker->randomElement(array('Nike', 'Adidas', 'Puma', 'Potato'));
        $this->addAttribute($product, 'T-Shirt brand', $randomBrand);

        // T-Shirt collection.
        $randomCollection = sprintf('Symfony2 %s %s', $this->faker->randomElement(array('Summer', 'Winter', 'Spring', 'Autumn')), rand(1995, 2012));
        $this->addAttribute($product, 'T-Shirt collection', $randomCollection);

        // T-Shirt material.
        $randomMaterial = $this->faker->randomElement(array('Polyester', 'Wool', 'Polyester 10% / Wool 90%', 'Potato 100%'));
        $this->addAttribute($product, 'T-Shirt material', $randomMaterial);

        $product->addOption($this->getReference('Sylius.Option.T-Shirt size'));
        $product->addOption($this->getReference('Sylius.Option.T-Shirt color'));

        $this->generateVariants($product);

        $this->setReference('Sylius.Product-'.$i, $product);

        return $product;
    }

    /**
     * Create sticker product.
     *
     * @param integer $i
     *
     * @return ProductInterface
     */
    protected function createSticker($i)
    {
        $product = $this->createProduct();

        $product->setTaxCategory($this->getTaxCategory('Taxable goods'));
        $product->setName(sprintf('Sticker "%s"', $this->faker->word));
        $product->setDescription($this->faker->paragraph);
        $product->setShortDescription($this->faker->sentence);
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        $this->addMasterVariant($product);

        $this->setTaxons($product, array('Stickers', 'Stickypicky'));

        // Sticker resolution.
        $randomResolution = $this->faker->randomElement(array('Waka waka', 'FULL HD', '300DPI', '200DPI'));
        $this->addAttribute($product, 'Sticker resolution', $randomResolution);

        // Sticker paper.
        $randomPaper = sprintf('Paper from tree %s', $this->faker->randomElement(array('Wung', 'Yang', 'Lemon-San', 'Me-Gusta')));
        $this->addAttribute($product, 'Sticker paper', $randomPaper);

        $product->addOption($this->getReference('Sylius.Option.Sticker size'));

        $this->generateVariants($product);

        $this->setReference('Sylius.Product.'.$i, $product);

        return $product;
    }

    /**
     * Create mug product.
     *
     * @param integer $i
     *
     * @return ProductInterface
     */
    protected function createMug($i)
    {
        $product = $this->createProduct();

        $product->setTaxCategory($this->getTaxCategory('Taxable goods'));
        $product->setName(sprintf('Mug "%s"', $this->faker->word));
        $product->setDescription($this->faker->paragraph);
        $product->setShortDescription($this->faker->sentence);

        $this->addMasterVariant($product);

        $this->setTaxons($product, array('Mugs', 'Mugland'));

        $randomMugMaterial = $this->faker->randomElement(array('Invisible porcelain', 'Banana skin', 'Porcelain', 'Sand'));
        $this->addAttribute($product, 'Mug material', $randomMugMaterial);

        $product->addOption($this->getReference('Sylius.Option.Mug type'));

        $this->generateVariants($product);

        $this->setReference('Sylius.Product.'.$i, $product);

        return $product;
    }

    /**
     * Create book product.
     *
     * @param integer $i
     *
     * @return ProductInterface
     */
    protected function createBook($i)
    {
        $product = $this->createProduct();

        $author = $this->faker->name;
        $isbn = $this->getUniqueISBN();

        $product->setTaxCategory($this->getTaxCategory('Taxable goods'));
        $product->setName(sprintf('Book "%s" by "%s"', ucfirst($this->faker->word), $author));
        $product->setDescription($this->faker->paragraph);
        $product->setShortDescription($this->faker->sentence);

        $this->addMasterVariant($product, $isbn);

        $this->setTaxons($product, array('Books', 'Bookmania'));

        $this->addAttribute($product, 'Book author', $author);
        $this->addAttribute($product, 'Book ISBN', $isbn);
        $this->addAttribute($product, 'Book pages', $this->faker->randomNumber(3));

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
            $variant->setSku($this->getUniqueSku());
            $variant->setOnHand($this->faker->randomNumber(1));

            $this->setReference('Sylius.Variant-'.$this->totalVariants, $variant);

            ++$this->totalVariants;
        }
    }

    /**
     * Adds master variant to product.
     *
     * @param ProductInterface $product
     * @param string           $sku
     */
    protected function addMasterVariant(ProductInterface $product, $sku = null)
    {
        $variant = $product->getMasterVariant();
        $variant->setProduct($product);
        $variant->setPrice($this->faker->randomNumber(4));
        $variant->setSku(null === $sku ? $this->getUniqueSku() : $sku);
        $variant->setAvailableOn($this->faker->dateTimeThisYear);
        $variant->setOnHand($this->faker->randomNumber(1));

        $productName = explode(' ', $product->getName());
        $image = clone $this->getReference(
            'Sylius.Image.'.strtolower($productName[0])
        );
        $variant->addImage($image);

        $this->setReference('Sylius.Variant-'.$this->totalVariants, $variant);

        ++$this->totalVariants;

        $product->setMasterVariant($variant);
    }

    /**
     * Adds attribute to product with given value.
     *
     * @param ProductInterface $product
     * @param string           $name
     * @param string           $value
     */
    private function addAttribute(ProductInterface $product, $name, $value)
    {
        /* @var $attribute AttributeValueInterface */
        $attribute = $this->getProductAttributeValueRepository()->createNew();
        $attribute->setAttribute($this->getReference('Sylius.Attribute.'.$name));
        $attribute->setProduct($product);
        $attribute->setValue($value);

        $product->addAttribute($attribute);
    }

    /**
     * Add product to given taxons.
     *
     * @param ProductInterface $product
     * @param array            $taxonNames
     */
    protected function setTaxons(ProductInterface $product, array $taxonNames)
    {
        $taxons = new ArrayCollection();

        foreach ($taxonNames as $taxonName) {
            $taxons->add($this->getReference('Sylius.Taxon.'.$taxonName));
        }

        $product->setTaxons($taxons);
    }

    /**
     * Get tax category by name.
     *
     * @param string $name
     *
     * @return TaxCategoryInterface
     */
    protected function getTaxCategory($name)
    {
        return $this->getReference('Sylius.TaxCategory.'.ucfirst($name));
    }

    /**
     * Get unique SKU.
     *
     * @param integer $length
     *
     * @return string
     */
    protected function getUniqueSku($length = 5)
    {
        return $this->faker->unique()->randomNumber($length);
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
     * Create new product instance.
     *
     * @return ProductInterface
     */
    protected function createProduct()
    {
        return $this->getProductRepository()->createNew();
    }

    /**
     * Define constant with number of total variants created.
     */
    protected function defineTotalVariants()
    {
        define('SYLIUS_FIXTURES_TOTAL_VARIANTS', $this->totalVariants);
    }
}
