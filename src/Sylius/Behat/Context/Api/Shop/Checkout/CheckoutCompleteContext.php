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

namespace Sylius\Behat\Context\Api\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Webmozart\Assert\Assert;

final class CheckoutCompleteContext implements Context
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private SharedStorageInterface $sharedStorage,
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @Given I have confirmed order
     */
    public function iConfirmMyOrder(): void
    {
        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $this->sharedStorage->get('cart_token'),
            HTTPRequest::METHOD_PATCH,
            'complete',
        );

        $this->client->executeCustomRequest($request);
    }

    /**
     * @Then /^I should be informed that (this variant) has been disabled$/
     */
    public function iShouldBeInformedThatThisVariantHasBeenDisabled(ProductVariantInterface $productVariant): void
    {
        $lastResponseContent = $this->client->getLastResponse()->getContent();

        Assert::string($lastResponseContent);
        Assert::contains($lastResponseContent, sprintf('This product %s has been disabled.', $productVariant->getName()));
    }

    /**
     * @Then my order should not be placed due to changed order total
     */
    public function myOrderShouldNotBePlacedDueToChangedOrderTotal(): void
    {
        $lastResponseContent = $this->client->getLastResponse()->getContent();

        Assert::string($lastResponseContent);
        Assert::contains($lastResponseContent, 'Order total has changed during checkout process');
    }
}
