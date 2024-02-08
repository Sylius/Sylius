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
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class RemovingProductContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I (try to) delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product): void
    {
        $this->client->delete(Resources::PRODUCTS, $product->getCode());
    }

    /**
     * @Then /^(this product) should still exist$/
     */
    public function theProductShouldStillExist(ProductInterface $product): void
    {
        $this->client->show(Resources::PRODUCTS, $product->getCode());

        Assert::true($this->responseChecker->isShowSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then I should be notified that this product could not be deleted as it is in use by a promotion rule
     */
    public function iShouldBeNotifiedThatThisProductCouldNotBeDeleted(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete a product that is in use by a promotion rule.',
        );
    }
}
