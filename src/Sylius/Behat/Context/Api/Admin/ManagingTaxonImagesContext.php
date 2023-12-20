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
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class ManagingTaxonImagesContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private \ArrayAccess $minkParameters,
    ) {
    }

    /**
     * @When /^I attach the "([^"]+)" image with "([^"]+)" type to (this taxon)$/
     */
    public function iAttachTheImageWithTypeToThisTaxon(string $path, ?string $type, TaxonInterface $taxon): void
    {
        $builder = RequestBuilder::create(
            sprintf('/api/v2/admin/taxons/%s/images', $taxon->getCode()),
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
     * @When /^I attach the "([^"]+)" image to (this taxon)$/
     */
    public function iAttachTheImageToThisTaxon(string $path, TaxonInterface $taxon): void
    {
        $this->iAttachTheImageWithTypeToThisTaxon($path, null, $taxon);
    }

    /**
     * @When I( also) remove an image with :type type
     */
    public function iRemoveAnImageWithType(string $type): void
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->sharedStorage->get('taxon');

        $taxonImage = $taxon->getImagesByType($type)->first();
        Assert::notFalse($taxonImage);

        $this->client->delete(Resources::TAXON_IMAGES, (string) $taxonImage->getId());
    }

    /**
     * @When I remove the first image
     */
    public function iRemoveTheFirstImage(): void
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->sharedStorage->get('taxon');

        $taxonImage = $taxon->getImages()->first();
        Assert::notFalse($taxonImage);

        $this->client->delete(Resources::TAXON_IMAGES, (string) $taxonImage->getId());
    }

    /**
     * @When I change the first image type to :type
     */
    public function iChangeTheFirstImageTypeTo(string $type): void
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->sharedStorage->get('taxon');

        $taxonImage = $taxon->getImages()->first();
        Assert::notFalse($taxonImage);

        $this->client->buildUpdateRequest(Resources::TAXON_IMAGES, (string) $taxonImage->getId());
        $this->client->updateRequestData(['type' => $type]);
        $this->client->update();
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
     * @Then /^(this taxon) should(?:| also) have an image with "([^"]*)" type$/
     * @Then /^(it) should(?:| also) have an image with "([^"]*)" type$/
     */
    public function thisTaxonShouldHaveAnImageWithType(TaxonInterface $taxon, string $type): void
    {
        Assert::true($this->responseChecker->hasValuesInAnySubresourceObjectCollection(
            $this->client->show(Resources::TAXONS, $taxon->getCode()),
            'images',
            ['type' => $type],
        ));
    }

    /**
     * @Then /^(this taxon) should not have(?:| also) any images with "([^"]*)" type$/
     * @Then /^(it) should not have(?:| also) any images with "([^"]*)" type$/
     */
    public function thisTaxonShouldNotHaveAnyImagesWithType(TaxonInterface $taxon, string $type): void
    {
        Assert::false($this->responseChecker->hasValuesInAnySubresourceObjectCollection(
            $this->client->show(Resources::TAXONS, $taxon->getCode()),
            'images',
            ['type' => $type],
        ));
    }

    /**
     * @Then /^(this taxon) should have only one image$/
     * @Then /^(this taxon) should(?:| still) have (\d+) images?$/
     */
    public function thisTaxonShouldHaveImages(TaxonInterface $taxon, int $count = 1): void
    {
        Assert::count(
            $this->responseChecker->getValue($this->client->show(Resources::TAXONS, $taxon->getCode()), 'images'),
            $count,
        );
    }

    /**
     * @Then /^(this taxon) should not have any images$/
     */
    public function thisTaxonShouldNotHaveAnyImages(TaxonInterface $taxon): void
    {
        $this->thisTaxonShouldHaveImages($taxon, 0);
    }
}
