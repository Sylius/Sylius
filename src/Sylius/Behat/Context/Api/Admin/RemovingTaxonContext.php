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
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class RemovingTaxonContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I (try to) delete taxon named :taxon
     */
    public function iDeleteTaxon(TaxonInterface $taxon): void
    {
        $this->client->delete(Resources::TAXONS, $taxon->getCode());
        $this->sharedStorage->set('taxon', $taxon);
    }

    /**
     * @Then /^(this taxon) should still exist$/
     */
    public function theTaxonShouldStillExist(TaxonInterface $taxon): void
    {
        $this->client->show(Resources::TAXONS, $taxon->getCode());

        Assert::true($this->responseChecker->isShowSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then I should be notified that this taxon could not be deleted as it is in use by a promotion rule
     */
    public function iShouldBeNotifiedThatThisTaxonCouldNotBeDeleted(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete a taxon that is in use by a promotion rule.',
        );
    }
}
