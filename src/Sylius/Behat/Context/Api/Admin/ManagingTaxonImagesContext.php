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
     * @Then /^I attach the "([^"]+)" image with "([^"]+)" type to (this taxon)$/
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
     * @Then /^I attach the "([^"]+)" image to (this taxon)$/
     */
    public function iAttachTheImageToThisTaxon(string $path, TaxonInterface $taxon): void
    {
        $this->iAttachTheImageWithTypeToThisTaxon($path, null, $taxon);
    }

    /**
     * @Then I should be notified that it has been successfully uploaded
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyUploaded(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            sprintf(
                'Resource could not be created: %s',
                $this->responseChecker->getError($this->client->getLastResponse()),
            ),
        );
    }

    /**
     * @Then /^(this taxon) should(?:| also) have an image with "([^"]*)" type$/
     * @Then /^(it) should(?:| also) have an image with "([^"]*)" type$/
     */
    public function thisTaxonShouldHaveAnImageWithType(TaxonInterface $taxon, string $type): void
    {
        Assert::true($this->responseChecker->hasSubResourceWithValue(
            $this->client->show(Resources::TAXONS, $taxon->getCode()),
            'images',
            'type',
            $type,
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
}
