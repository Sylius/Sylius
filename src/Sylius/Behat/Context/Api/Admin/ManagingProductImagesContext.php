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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
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
    ) {
    }

    /**
     * @When /^I attach the "([^"]+)" image with "([^"]+)" type to (this product)$/
     */
    public function iAttachTheImageWithTypeToThisProduct(string $path, ?string $type, ProductInterface $product): void
    {
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

        $this->client->request($builder->build());
    }

    /**
     * @When /^I attach the "([^"]+)" image to (this product)$/
     */
    public function iAttachTheImageToThisProduct(string $path, ProductInterface $product): void
    {
        $this->iAttachTheImageWithTypeToThisProduct($path, null, $product);
    }

    /**
     * @Then the product :product should have an image with :type type
     * @Then /^(it) should(?:| also) have an image with "([^"]*)" type$/
     */
    public function theProductShouldHaveAnImageWithType(ProductInterface $product, string $type): void
    {
        Assert::true($this->responseChecker->hasSubResourceWithValue(
            $this->client->show(Resources::PRODUCTS, $product->getCode()),
            'images',
            'type',
            $type,
        ));
    }

    /**
     * @Then /^(this product) should have only one image$/
     * @Then /^(this product) should(?:| still) have (\d+) images?$/
     */
    public function thisProductShouldHaveImages(ProductInterface $product, int $count = 1): void
    {
        Assert::count(
            $this->responseChecker->getValue($this->client->show(Resources::PRODUCTS, $product->getCode()), 'images'),
            $count,
        );
    }
}
