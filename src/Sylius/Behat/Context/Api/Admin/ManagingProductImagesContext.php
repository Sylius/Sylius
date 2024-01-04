<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestBuilder;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverter;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class ManagingProductImagesContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private \ArrayAccess $minkParameters,
        private SectionAwareIriConverter $sectionAwareIriConverter,
    ) {
    }

    /**
     * @When /^I attach the "([^"]+)" image with "([^"]+)" type to (this product)$/
     */
    public function iAttachTheImageWithTypeToThisProduct(string $path, string $type, ProductInterface $product): void
    {
        $this->createProductImage($path, $product, $type);
    }

    /**
     * @When /^I attach the "([^"]+)" image to (this product)$/
     */
    public function iAttachTheImageToThisProduct(string $path, ProductInterface $product): void
    {
        $this->createProductImage($path, $product);
    }

    /**
     * @When /^I attach the "([^"]+)" image with selected ("[^"]+" variant) to (this product)$/
     */
    public function iAttachImageWithSelectedVariantToThisProduct(
        string $path,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
    ): void {
        $this->createProductImage($path, $product, null, [$productVariant]);
    }

    /**
     * @When I( also) remove an image with :type type
     */
    public function iRemoveAnImageWithType(string $type): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        $productImage = $product->getImagesByType($type)->first();
        Assert::notFalse($productImage);

        $this->client->delete(Resources::PRODUCT_IMAGES, (string) $productImage->getId());
    }

    /**
     * @When I remove the first image
     */
    public function iRemoveTheFirstImage(): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        $productImage = $product->getImages()->first();
        Assert::notFalse($productImage);

        $this->client->delete(Resources::PRODUCT_IMAGES, (string) $productImage->getId());
    }

    /**
     * @When I change the first image type to :type
     */
    public function iChangeTheFirstImageTypeTo(string $type): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        $productImage = $product->getImages()->first();
        Assert::notFalse($productImage);

        $this->client->buildUpdateRequest(Resources::PRODUCT_IMAGES, (string) $productImage->getId());
        $this->client->updateRequestData(['type' => $type]);
        $this->client->update();
    }

    /**
     * @When I select :productVariant variant for the first image
     */
    public function iSelectVariantForTheFirstImage(ProductVariantInterface $productVariant): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        $productImage = $product->getImages()->first();
        Assert::notFalse($productImage);

        $this->client->buildUpdateRequest(Resources::PRODUCT_IMAGES, (string) $productImage->getId());
        $this->client->updateRequestData(['productVariants' => [$this->sectionAwareIriConverter->getIriFromResourceInSection($productVariant, 'admin')]]);
        $this->client->update();
    }

    /**
     * @Then the product :product should have an image with :type type
     * @Then /^(this product) should(?:| also) have an image with "([^"]*)" type$/
     * @Then /^(it) should(?:| also) have an image with "([^"]*)" type$/
     */
    public function theProductShouldHaveAnImageWithType(ProductInterface $product, string $type): void
    {
        Assert::true(
            $this->responseChecker->hasValuesInAnySubresourceObjectCollection(
                $this->client->show(Resources::PRODUCTS, $product->getCode()),
                'images',
                ['type' => $type],
            ),
            sprintf('Product %s does not have an image with %s type', $product->getName(), $type),
        );
    }

    /**
     * @Then its image should have :productVariant variant selected
     */
    public function itsImageShouldHaveVariantSelected(ProductVariantInterface $productVariant): void
    {
        $images = $this->responseChecker->getValue(
            $this->client->getLastResponse(),
            'images',
        );

        Assert::notEmpty($images);
        Assert::inArray(
            $this->sectionAwareIriConverter->getIriFromResourceInSection($productVariant, 'admin'),
            $images[0]['productVariants'],
        );
    }

    /**
     * @Then /^(this product) should not have(?:| also) any images with "([^"]*)" type$/
     * @Then /^(it) should not have(?:| also) any images with "([^"]*)" type$/
     */
    public function thisProductShouldNotHaveAnyImagesWithType(ProductInterface $product, string $type): void
    {
        Assert::false(
            $this->responseChecker->hasValuesInAnySubresourceObjectCollection(
                $this->client->show(Resources::PRODUCTS, $product->getCode()),
                'images',
                ['type' => $type],
            ),
            sprintf('Product %s does not have an image with %s type', $product->getName(), $type),
        );
    }

    /**
     * @Then /^(this product) should(?:| still) have only one image$/
     * @Then /^(this product) should(?:| still) have (\d+) images?$/
     */
    public function thisProductShouldHaveImages(ProductInterface $product, int $count = 1): void
    {
        Assert::count(
            $this->responseChecker->getValue($this->client->show(Resources::PRODUCTS, $product->getCode()), 'images'),
            $count,
        );
    }

    /**
     * @Then /^(this product) should not have any images$/
     */
    public function thisProductShouldNotHaveAnyImages(ProductInterface $product): void
    {
        $this->thisProductShouldHaveImages($product, 0);
    }

    /**
     * @Then I should be notified that the changes have been successfully applied
     */
    public function iShouldBeNotifiedThatTheChangesHaveBeenSuccessfullyApplied(): void
    {
        $response = $this->client->getLastResponse();
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($response) || $this->responseChecker->isUpdateSuccessful($response),
        );
    }

    /**
     * @Then /^I should be notified that the ("[^"]+" variant) does not belong to (this product)$/
     */
    public function iShouldBeNotifiedThatTheProductVariantDoesNotBelongToTheOwner(ProductVariantInterface $productVariant, ProductInterface $product): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf(
                'The product variant with code "%s" does not belong to the product with code "%s", which is the owner of the image.',
                $productVariant->getCode(),
                $product->getCode(),
            ),
        );
    }

    private function createProductImage(
        string $path,
        ProductInterface $product,
        ?string $type = null,
        array $variants = [],
    ): void {
        $builder = RequestBuilder::create(
            sprintf('/api/v2/admin/products/%s/images', $product->getCode()),
            Request::METHOD_POST,
        );
        $builder->withHeader('CONTENT_TYPE', 'multipart/form-data');
        $builder->withHeader('HTTP_ACCEPT', 'application/ld+json');
        $builder->withHeader('HTTP_Authorization', 'Bearer ' . $this->sharedStorage->get('token'));
        $builder->withFile('file', new UploadedFile($this->minkParameters['files_path'] . $path, basename($path)));

        if (null !== $type) {
            $builder->withParameter('type', $type);
        }

        if (0 !== count($variants)) {
            $variantsIris = [];
            foreach ($variants as $variant) {
                $variantsIris[] = $this->sectionAwareIriConverter->getIriFromResourceInSection($variant, 'admin');
            }
            $builder->withParameter('productVariants', $variantsIris);
        }

        $this->client->request($builder->build());
    }
}
