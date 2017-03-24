<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductFactoryInterface
     */
    private $productFactory;

    /**
     * @var FactoryInterface
     */
    private $productTranslationFactory;

    /**
     * @var FactoryInterface
     */
    private $productVariantFactory;

    /**
     * @var FactoryInterface
     */
    private $productVariantTranslationFactory;

    /**
     * @var FactoryInterface
     */
    private $channelPricingFactory;

    /**
     * @var FactoryInterface
     */
    private $productOptionFactory;

    /**
     * @var FactoryInterface
     */
    private $productOptionValueFactory;

    /**
     * @var FactoryInterface
     */
    private $productImageFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ProductVariantGeneratorInterface
     */
    private $productVariantGenerator;

    /**
     * @var ProductVariantResolverInterface
     */
    private $defaultVariantResolver;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;

    /**
     * @var array
     */
    private $minkParameters;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactoryInterface $productFactory
     * @param FactoryInterface $productTranslationFactory
     * @param FactoryInterface $productVariantFactory
     * @param FactoryInterface $productVariantTranslationFactory
     * @param FactoryInterface $channelPricingFactory
     * @param FactoryInterface $productOptionFactory
     * @param FactoryInterface $productOptionValueFactory
     * @param FactoryInterface $productImageFactory
     * @param ObjectManager $objectManager
     * @param ProductVariantGeneratorInterface $productVariantGenerator
     * @param ProductVariantResolverInterface $defaultVariantResolver
     * @param ImageUploaderInterface $imageUploader
     * @param SlugGeneratorInterface $slugGenerator
     * @param array $minkParameters
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        ProductFactoryInterface $productFactory,
        FactoryInterface $productTranslationFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $productVariantTranslationFactory,
        FactoryInterface $channelPricingFactory,
        FactoryInterface $productOptionFactory,
        FactoryInterface $productOptionValueFactory,
        FactoryInterface $productImageFactory,
        ObjectManager $objectManager,
        ProductVariantGeneratorInterface $productVariantGenerator,
        ProductVariantResolverInterface $defaultVariantResolver,
        ImageUploaderInterface $imageUploader,
        SlugGeneratorInterface $slugGenerator,
        array $minkParameters
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productTranslationFactory = $productTranslationFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->productVariantTranslationFactory = $productVariantTranslationFactory;
        $this->channelPricingFactory = $channelPricingFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
        $this->productImageFactory = $productImageFactory;
        $this->objectManager = $objectManager;
        $this->productVariantGenerator = $productVariantGenerator;
        $this->defaultVariantResolver = $defaultVariantResolver;
        $this->imageUploader = $imageUploader;
        $this->slugGenerator = $slugGenerator;
        $this->minkParameters = $minkParameters;
    }

    /**
     * @Given the store has a product :productName
     * @Given the store has a :productName product
     * @Given I added a product :productName
     * @Given /^the store(?:| also) has a product "([^"]+)" priced at ("[^"]+")$/
     * @Given /^the store(?:| also) has a product "([^"]+)" priced at ("[^"]+") in ("[^"]+" channel)$/
     */
    public function storeHasAProductPricedAt($productName, $price = 100, ChannelInterface $channel = null)
    {
        $product = $this->createProduct($productName, $price, $channel);

        $this->saveProduct($product);
    }

    /**
     * @Given /^(this product) is also priced at ("[^"]+") in ("[^"]+" channel)$/
     */
    public function thisProductIsAlsoPricedAtInChannel(ProductInterface $product, $price, ChannelInterface $channel)
    {
        $product->addChannel($channel);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->addChannelPricing($this->createChannelPricingForChannel($price, $channel));

        $this->objectManager->flush();
    }

    /**
     * @Given the store( also) has a product :productName with code :code
     * @Given the store( also) has a product :productName with code :code, created at :date
     */
    public function storeHasProductWithCode($productName, $code, $date = null)
    {
        $product = $this->createProduct($productName);
        $product->setCreatedAt(new \DateTime($date));
        $product->setCode($code);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the store(?:| also) has a product "([^"]+)" priced at ("[^"]+") available in (channel "[^"]+") and (channel "[^"]+")$/
     */
    public function storeHasAProductPricedAtAvailableInChannels($productName, $price = 100, ...$channels)
    {
        $product = $this->createProduct($productName, $price);
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);

        foreach ($channels as $channel) {
            $product->addChannel($channel);
            if (!$productVariant->hasChannelPricingForChannel($channel)) {
                $productVariant->addChannelPricing($this->createChannelPricingForChannel($price, $channel));
            }
        }

        $this->saveProduct($product);
    }

    /**
     * @Given /^(this product) is named "([^"]+)" (in the "([^"]+)" locale)$/
     * @Given /^the (product "[^"]+") is named "([^"]+)" (in the "([^"]+)" locale)$/
     */
    public function thisProductIsNamedIn(ProductInterface $product, $name, $locale)
    {
        $this->addProductTranslation($product, $name, $locale);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the store has a product named "([^"]+)" in ("[^"]+" locale) and "([^"]+)" in ("[^"]+" locale)$/
     */
    public function theStoreHasProductNamedInAndIn($firstName, $firstLocale, $secondName, $secondLocale)
    {
        $product = $this->createProduct($firstName);

        $names = [$firstName => $firstLocale, $secondName => $secondLocale];
        foreach ($names as $name => $locale) {
            $this->addProductTranslation($product, $name, $locale);
        }

        $this->saveProduct($product);
    }

    /**
     * @Given /^the store has(?:| a| an) "([^"]+)" configurable product$/
     * @Given /^the store has(?:| a| an) "([^"]+)" configurable product with "([^"]+)" slug$/
     */
    public function storeHasAConfigurableProduct($productName, $slug = null)
    {
        /** @var ChannelInterface|null $channel */
        $channel = null;
        if ($this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
        }

        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setCode(StringInflector::nameToUppercaseCode($productName));

        if (null !== $channel) {
            $product->addChannel($channel);

            foreach ($channel->getLocales() as $locale) {
                $product->setFallbackLocale($locale->getCode());
                $product->setCurrentLocale($locale->getCode());

                $product->setName($productName);
                $product->setSlug($slug ?: $this->slugGenerator->generate($productName));
            }
        }

        $this->saveProduct($product);
    }

    /**
     * @Given the store has( also) :firstProductName and :secondProductName products
     * @Given the store has( also) :firstProductName, :secondProductName and :thirdProductName products
     * @Given the store has( also) :firstProductName, :secondProductName, :thirdProductName and :fourthProductName products
     */
    public function theStoreHasProducts(...$productsNames)
    {
        foreach ($productsNames as $productName) {
            $this->saveProduct($this->createProduct($productName));
        }
    }

    /**
     * @Given /^(this channel) has "([^"]+)", "([^"]+)", "([^"]+)" and "([^"]+)" products$/
     */
    public function thisChannelHasProducts(ChannelInterface $channel, ...$productsNames)
    {
        foreach ($productsNames as $productName) {
            $product = $this->createProduct($productName, 0, $channel);

            $this->saveProduct($product);
        }
    }

    /**
     * @Given /^the (product "[^"]+") has(?:| a) "([^"]+)" variant priced at ("[^"]+")$/
     * @Given /^(this product) has "([^"]+)" variant priced at ("[^"]+")$/
     * @Given /^(this product) has "([^"]+)" variant priced at ("[^"]+") in ("([^"]+)" channel)$/
     */
    public function theProductHasVariantPricedAt(
        ProductInterface $product,
        $productVariantName,
        $price,
        ChannelInterface $channel = null
    ) {
        $this->createProductVariant(
            $product,
            $productVariantName,
            $price,
            StringInflector::nameToUppercaseCode($productVariantName),
            (null !== $channel) ? $channel : $this->sharedStorage->get('channel')
        );
    }

    /**
     * @Given /^the (product "[^"]+") has(?:| a| an) "([^"]+)" variant$/
     * @Given /^(this product) has(?:| a| an) "([^"]+)" variant$/
     * @Given /^(this product) has "([^"]+)", "([^"]+)" and "([^"]+)" variants$/
     */
    public function theProductHasVariants(ProductInterface $product, ...$variantNames)
    {
        $channel = $this->sharedStorage->get('channel');

        foreach ($variantNames as $name) {
            $this->createProductVariant(
                $product,
                $name,
                0,
                StringInflector::nameToUppercaseCode($name),
                $channel
            );
        }
    }

    /**
     * @Given /^the (product "[^"]+")(?:| also) has a nameless variant with code "([^"]+)"$/
     * @Given /^(this product)(?:| also) has a nameless variant with code "([^"]+)"$/
     * @Given /^(it)(?:| also) has a nameless variant with code "([^"]+)"$/
     */
    public function theProductHasNamelessVariantWithCode(ProductInterface $product, $variantCode)
    {
        $channel = $this->sharedStorage->get('channel');

        $this->createProductVariant($product, null, 0, $variantCode, $channel);
    }

    /**
     * @Given /^the (product "[^"]+")(?:| also) has(?:| a| an) "([^"]+)" variant with code "([^"]+)"$/
     * @Given /^(this product)(?:| also) has(?:| a| an) "([^"]+)" variant with code "([^"]+)"$/
     * @Given /^(it)(?:| also) has(?:| a| an) "([^"]+)" variant with code "([^"]+)"$/
     */
    public function theProductHasVariantWithCode(ProductInterface $product, $variantName, $variantCode)
    {
        $channel = $this->sharedStorage->get('channel');

        $this->createProductVariant($product, $variantName, 0, $variantCode, $channel);
    }

    /**
     * @Given /^(this product) has "([^"]+)" variant priced at ("[^"]+") which does not require shipping$/
     */
    public function theProductHasVariantWhichDoesNotRequireShipping(
        ProductInterface $product,
        $productVariantName,
        $price
    ) {
        $this->createProductVariant(
            $product,
            $productVariantName,
            $price,
            StringInflector::nameToUppercaseCode($productVariantName),
            $this->sharedStorage->get('channel'),
            null,
            false
        );
    }

    /**
     * @Given /^the (product "[^"]+") has(?:| also)(?:| a| an) "([^"]+)" variant$/
     * @Given /^the (product "[^"]+") has(?:| also)(?:| a| an) "([^"]+)" variant at position ([^"]+)$/
     * @Given /^(this product) has(?:| also)(?:| a| an) "([^"]+)" variant at position ([^"]+)$/
     */
    public function theProductHasVariantAtPosition(
        ProductInterface $product,
        $productVariantName,
        $position = null
    ) {
        $this->createProductVariant(
            $product,
            $productVariantName,
            0,
            StringInflector::nameToUppercaseCode($productVariantName),
            $this->sharedStorage->get('channel'),
            $position
        );
    }

    /**
     * @Given /^(this variant) is also priced at ("[^"]+") in ("([^"]+)" channel)$/
     */
    public function thisVariantIsAlsoPricedAtInChannel(ProductVariantInterface $productVariant, $price, ChannelInterface $channel)
    {
        $productVariant->addChannelPricing($this->createChannelPricingForChannel(
            $this->getPriceFromString(str_replace(['$', '€', '£'], '', $price)),
            $channel
        ));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(it|this product) has(?:| also) variant named "([^"]+)" in ("[^"]+" locale) and "([^"]+)" in ("[^"]+" locale)$/
     */
    public function itHasVariantNamedInAndIn(ProductInterface $product, $firstName, $firstLocale, $secondName, $secondLocale)
    {
        $productVariant = $this->createProductVariant(
            $product,
            $firstName,
            100,
            StringInflector::nameToUppercaseCode($firstName),
            $this->sharedStorage->get('channel')
        );

        $names = [$firstName => $firstLocale, $secondName => $secondLocale];
        foreach ($names as $name => $locale) {
            $this->addProductVariantTranslation($productVariant, $name, $locale);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has "([^"]+)" variant priced at ("[^"]+") identified by "([^"]+)"$/
     */
    public function theProductHasVariantPricedAtIdentifiedBy(
        ProductInterface $product,
        $productVariantName,
        $price,
        $code
    ) {
        $this->createProductVariant($product, $productVariantName, $price, $code, $this->sharedStorage->get('channel'));
    }

    /**
     * @Given /^(this product) only variant was renamed to "([^"]+)"$/
     */
    public function productOnlyVariantWasRenamed(ProductInterface $product, $variantName)
    {
        Assert::true($product->isSimple());

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();
        $productVariant->setName($variantName);

        $this->objectManager->flush();
    }

    /**
     * @Given /^there is product "([^"]+)" available in ((?:this|that|"[^"]+") channel)$/
     * @Given /^the store has a product "([^"]+)" available in ("([^"]+)" channel)$/
     */
    public function thereIsProductAvailableInGivenChannel($productName, ChannelInterface $channel)
    {
        $product = $this->createProduct($productName, 0, $channel);

        $this->saveProduct($product);
    }

    /**
     * @Given /^([^"]+) belongs to ("[^"]+" tax category)$/
     */
    public function productBelongsToTaxCategory(ProductInterface $product, TaxCategoryInterface $taxCategory)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->defaultVariantResolver->getVariant($product);
        $variant->setTaxCategory($taxCategory);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(it) comes in the following variations:$/
     */
    public function itComesInTheFollowingVariations(ProductInterface $product, TableNode $table)
    {
        $channel = $this->sharedStorage->get('channel');

        foreach ($table->getHash() as $variantHash) {
            /** @var ProductVariantInterface $variant */
            $variant = $this->productVariantFactory->createNew();

            $variant->setName($variantHash['name']);
            $variant->setCode(StringInflector::nameToUppercaseCode($variantHash['name']));
            $variant->addChannelPricing($this->createChannelPricingForChannel(
                $this->getPriceFromString(str_replace(['$', '€', '£'], '', $variantHash['price'])),
                $channel
            ));

            $variant->setProduct($product);
            $product->addVariant($variant);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^("[^"]+" variant of product "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function productVariantBelongsToTaxCategory(
        ProductVariantInterface $productVariant,
        TaxCategoryInterface $taxCategory
    ) {
        $productVariant->setTaxCategory($taxCategory);
        $this->objectManager->flush($productVariant);
    }

    /**
     * @Given /^(this product) has option "([^"]+)" with values "([^"]+)" and "([^"]+)"$/
     * @Given /^(this product) has option "([^"]+)" with values "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function thisProductHasOptionWithValues(ProductInterface $product, $optionName, ...$values)
    {
        /** @var ProductOptionInterface $option */
        $option = $this->productOptionFactory->createNew();

        $option->setName($optionName);
        $option->setCode(StringInflector::nameToUppercaseCode($optionName));

        $this->sharedStorage->set(sprintf('%s_option', $optionName), $option);

        foreach ($values as $key => $value) {
            $optionValue = $this->addProductOption($option, $value, StringInflector::nameToUppercaseCode($value));
            $this->sharedStorage->set(sprintf('%s_option_%s_value', $value, strtolower($optionName)), $optionValue);
        }

        $product->addOption($option);
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        $this->objectManager->persist($option);
        $this->objectManager->flush();
    }

    /**
     * @Given /^there (?:is|are) (\d+) unit(?:|s) of (product "([^"]+)") available in the inventory$/
     */
    public function thereIsQuantityOfProducts($quantity, ProductInterface $product)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setOnHand($quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the (product "([^"]+)") is out of stock$/
     */
    public function theProductIsOutOfStock(ProductInterface $product)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setTracked(true);
        $productVariant->setOnHand(0);

        $this->objectManager->flush();
    }

    /**
     * @When other customer has bought :quantity :product products by this time
     */
    public function otherCustomerHasBoughtProductsByThisTime($quantity, ProductInterface $product)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setOnHand($productVariant->getOnHand() - $quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) is tracked by the inventory$/
     * @Given /^(?:|the )("[^"]+" product) is(?:| also) tracked by the inventory$/
     */
    public function thisProductIsTrackedByTheInventory(ProductInterface $product)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setTracked(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) is available in "([^"]+)" ([^"]+) priced at ("[^"]+")$/
     */
    public function thisProductIsAvailableInSize(ProductInterface $product, $optionValueName, $optionName, $price)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();

        $optionValue = $this->sharedStorage->get(sprintf('%s_option_%s_value', $optionValueName, $optionName));

        $variant->addOptionValue($optionValue);
        $variant->addChannelPricing($this->createChannelPricingForChannel($price, $this->sharedStorage->get('channel')));
        $variant->setCode(sprintf('%s_%s', $product->getCode(), $optionValueName));
        $variant->setName($product->getName());

        $product->addVariant($variant);
        $this->objectManager->flush();
    }

    /**
     * @Given the :product product's :optionValueName size belongs to :shippingCategory shipping category
     */
    public function thisProductSizeBelongsToShippingCategory(ProductInterface $product, $optionValueName, ShippingCategoryInterface $shippingCategory)
    {
        $code = sprintf('%s_%s', $product->getCode(), $optionValueName);
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->filter(function ($variant) use ($code) {
            return $code === $variant->getCode();
        })->first();

        Assert::notNull($productVariant, sprintf('Product variant with given code %s not exists!', $code));

        $productVariant->setShippingCategory($shippingCategory);
        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has (this product option)$/
     * @Given /^(this product) has (?:a|an) ("[^"]+" option)$/
     */
    public function thisProductHasThisProductOption(ProductInterface $product, ProductOptionInterface $option)
    {
        $product->addOption($option);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has all possible variants$/
     */
    public function thisProductHasAllPossibleVariants(ProductInterface $product)
    {
        try {
            foreach ($product->getVariants() as $productVariant) {
                $product->removeVariant($productVariant);
            }

            $this->productVariantGenerator->generate($product);
        } catch (\InvalidArgumentException $exception) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $this->productVariantFactory->createNew();

            $product->addVariant($productVariant);
        }

        $i = 0;
        /** @var ProductVariantInterface $productVariant */
        foreach ($product->getVariants() as $productVariant) {
            $productVariant->setCode(sprintf('%s-variant-%d', $product->getCode(), $i));

            foreach ($product->getChannels() as $channel) {
                $productVariant->addChannelPricing($this->createChannelPricingForChannel(1000, $channel));
            }

            ++$i;
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^there are ([^"]+) units of ("[^"]+" variant of product "[^"]+") available in the inventory$/
     */
    public function thereAreItemsOfProductInVariantAvailableInTheInventory($quantity, ProductVariantInterface $productVariant)
    {
        $productVariant->setTracked(true);
        $productVariant->setOnHand($quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the ("[^"]+" product variant) is tracked by the inventory$/
     */
    public function theProductVariantIsTrackedByTheInventory(ProductVariantInterface $productVariant)
    {
        $productVariant->setTracked(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product)'s price is ("[^"]+")$/
     * @Given /^the (product "[^"]+") changed its price to ("[^"]+")$/
     * @Given /^(this product) price has been changed to ("[^"]+")$/
     */
    public function theProductChangedItsPriceTo(ProductInterface $product, $price)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $channelPricing = $productVariant->getChannelPricingForChannel($this->sharedStorage->get('channel'));
        $channelPricing->setPrice($price);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product)(?:| also) has an image "([^"]+)" with "([^"]+)" type$/
     * @Given /^the ("[^"]+" product)(?:| also) has an image "([^"]+)" with "([^"]+)" type$/
     * @Given /^(it)(?:| also) has an image "([^"]+)" with "([^"]+)" type$/
     */
    public function thisProductHasAnImageWithType(ProductInterface $product, $imagePath, $imageType)
    {
        $filesPath = $this->getParameter('files_path');

        /** @var ImageInterface $productImage */
        $productImage = $this->productImageFactory->createNew();
        $productImage->setFile(new UploadedFile($filesPath.$imagePath, basename($imagePath)));
        $productImage->setType($imageType);
        $this->imageUploader->upload($productImage);

        $product->addImage($productImage);

        $this->objectManager->flush($product);
    }

    /**
     * @Given /^(this product) belongs to ("([^"]+)" shipping category)$/
     * @Given product :product shipping category has been changed to :shippingCategory
     */
    public function thisProductBelongsToShippingCategory(ProductInterface $product, ShippingCategoryInterface $shippingCategory)
    {
        $product->getVariants()->first()->setShippingCategory($shippingCategory);
        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) has been disabled$/
     */
    public function thisProductHasBeenDisabled(ProductInterface $product)
    {
        $product->disable();
        $this->objectManager->flush();
    }

    /**
     * @param string $price
     *
     * @return int
     */
    private function getPriceFromString($price)
    {
        return (int) round($price * 100, 2);
    }

    /**
     * @param string $productName
     * @param int $price
     * @param ChannelInterface|null $channel
     *
     * @return ProductInterface
     */
    private function createProduct($productName, $price = 100, ChannelInterface $channel = null)
    {
        if (null === $channel && $this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
        }

        /** @var ProductInterface $product */
        $product = $this->productFactory->createWithVariant();

        $product->setCode(StringInflector::nameToUppercaseCode($productName));
        $product->setName($productName);
        $product->setSlug($this->slugGenerator->generate($productName));

        if (null !== $channel) {
            $product->addChannel($channel);

            foreach ($channel->getLocales() as $locale) {
                $product->setFallbackLocale($locale->getCode());
                $product->setCurrentLocale($locale->getCode());

                $product->setName($productName);
                $product->setSlug($this->slugGenerator->generate($productName));
            }
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);

        if (null !== $channel) {
            $productVariant->addChannelPricing($this->createChannelPricingForChannel($price, $channel));
        }

        $productVariant->setCode($product->getCode());
        $productVariant->setName($product->getName());

        return $product;
    }

    /**
     * @param ProductOptionInterface $option
     * @param string $value
     * @param string $code
     *
     * @return ProductOptionValueInterface
     */
    private function addProductOption(ProductOptionInterface $option, $value, $code)
    {
        /** @var ProductOptionValueInterface $optionValue */
        $optionValue = $this->productOptionValueFactory->createNew();

        $optionValue->setValue($value);
        $optionValue->setCode($code);
        $optionValue->setOption($option);

        $option->addValue($optionValue);

        return $optionValue;
    }

    /**
     * @param ProductInterface $product
     */
    private function saveProduct(ProductInterface $product)
    {
        $this->productRepository->add($product);
        $this->sharedStorage->set('product', $product);
    }

    /**
     * @param string $name
     *
     * @return NodeElement
     */
    private function getParameter($name)
    {
        return isset($this->minkParameters[$name]) ? $this->minkParameters[$name] : null;
    }

    /**
     * @param ProductInterface $product
     * @param $productVariantName
     * @param int $price
     * @param string $code
     * @param ChannelInterface $channel
     * @param int $position
     * @param bool $shippingRequired
     *
     * @return ProductVariantInterface
     */
    private function createProductVariant(
        ProductInterface $product,
        $productVariantName,
        $price,
        $code,
        ChannelInterface $channel = null,
        $position = null,
        $shippingRequired = true
    ) {
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_CHOICE);

        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();

        $variant->setName($productVariantName);
        $variant->setCode($code);
        $variant->setProduct($product);
        $variant->addChannelPricing($this->createChannelPricingForChannel($price, $channel));
        $variant->setPosition($position);
        $variant->setShippingRequired($shippingRequired);

        $product->addVariant($variant);

        $this->objectManager->flush();
        $this->sharedStorage->set('variant', $variant);

        return $variant;
    }

    /**
     * @param ProductInterface $product
     * @param string $name
     * @param string $locale
     */
    private function addProductTranslation(ProductInterface $product, $name, $locale)
    {
        /** @var ProductTranslationInterface|TranslationInterface $translation */
        $translation = $product->getTranslation($locale);
        if ($translation->getLocale() !== $locale) {
            $translation = $this->productTranslationFactory->createNew();
        }

        $translation->setLocale($locale);
        $translation->setName($name);
        $translation->setSlug($this->slugGenerator->generate($name));

        $product->addTranslation($translation);
    }

    /**
     * @param ProductVariantInterface $productVariant
     * @param string $name
     * @param string $locale
     */
    private function addProductVariantTranslation(ProductVariantInterface $productVariant, $name, $locale)
    {
        /** @var ProductVariantTranslationInterface|TranslationInterface $translation */
        $translation = $this->productVariantTranslationFactory->createNew();
        $translation->setLocale($locale);
        $translation->setName($name);

        $productVariant->addTranslation($translation);
    }

    /**
     * @param int $price
     * @param ChannelInterface|null $channel
     *
     * @return ChannelPricingInterface
     */
    private function createChannelPricingForChannel($price, ChannelInterface $channel = null)
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($price);
        $channelPricing->setChannelCode($channel->getCode());

        return $channelPricing;
    }
}
