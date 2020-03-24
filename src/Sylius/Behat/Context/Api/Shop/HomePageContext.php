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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class HomePageContext implements Context
{
    /** @var ApiClientInterface */
    private $productsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(
        ApiClientInterface $productsClient,
        ResponseCheckerInterface $responseChecker
    ) {
        $this->productsClient = $productsClient;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I check latest products
     */
    public function iCheckLatestProducts(): void
    {
        $this->productsClient->customAction(HttpRequest::METHOD_GET, 'new-api/products/get-latest');
    }

    /**
     * @Then I should see :count products in the list
     */
    public function iShouldSeeProductsInTheList(int $count): void
    {
        Assert::eq($this->responseChecker->countCollectionItems($this->productsClient->getLastResponse()), $count);
    }
}
