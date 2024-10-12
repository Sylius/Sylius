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

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Webmozart\Assert\Assert;

final readonly class CheckoutShippingContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private IriConverterInterface $iriConverter,
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
    ) {
    }

    #[When('I try to select non-existing shipping method')]
    public function iTryToSelectNonExistingShippingMethod(): void
    {
        if (!$this->responseChecker->isResponseContentOfClass($this->client->getLastResponse(), OrderInterface::class)) {
            $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));
        }

        $content = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        $this->client->requestPatch(
            uri: sprintf(
                'orders/%s/shipments/%s',
                $this->sharedStorage->get('cart_token'),
                $content['shipments'][0]['id'],
            ),
            body: ['shippingMethod' => '/api/v2/shop/shipping-methods/NON_EXISTING'],
        );
    }

    #[When('I complete the shipping step with first shipping method')]
    public function iCompleteTheShippingStepWithFirstShippingMethod(): void
    {
        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy([]);

        $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));

        $content = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        $this->client->requestPatch(
            uri: sprintf(
                'orders/%s/shipments/%s',
                $this->sharedStorage->get('cart_token'),
                $content['shipments'][0]['id'],
            ),
            body: ['shippingMethod' => $this->iriConverter->getIriFromResource($shippingMethod)],
        );
    }

    #[When('I change shipping method to :shippingMethod')]
    #[When('I chose :shippingMethod shipping method')]
    #[When('I proceed with :shippingMethod shipping method')]
    #[When('I select :shippingMethod shipping method')]
    #[When('I try to change shipping method to :shippingMethod')]
    #[When('I try to select :shippingMethod shipping method')]
    #[When('the customer has proceeded with :shippingMethod shipping method')]
    #[When('the customer proceeds with :shippingMethod shipping method')]
    #[When('the visitor has proceeded with :shippingMethod shipping method')]
    public function iTryToSelectShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        try {
            $response = $this->client->getLastResponse();
        } catch (\RuntimeException) {
            $response = $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));
        }

        if (!$this->responseChecker->isResponseContentOfClass($response, OrderInterface::class)) {
            $response = $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));
        }

        $content = $this->responseChecker->getResponseContent($response);

        $this->client->requestPatch(
            uri: sprintf(
                'orders/%s/shipments/%s',
                $this->sharedStorage->get('cart_token'),
                $content['shipments'][0]['id'],
            ),
            body: ['shippingMethod' => $this->iriConverter->getIriFromResource($shippingMethod)],
        );
    }

    #[Then('I should see that there is no assigned shipping method')]
    public function iShouldSeeThatThereIsNoAssignedShippingMethod(): void
    {
        $response = $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));

        Assert::isEmpty($this->responseChecker->getValue($response, 'shipments'));
    }

    #[Then('there should not be any shipping method available to choose')]
    public function thereShouldNotBeAnyShippingMethodAvailableToChoose(): void
    {
        $response = $this->client->requestGet('shipping-methods');

        Assert::isEmpty($this->responseChecker->getCollection($response));
    }

    #[Then('I should not be able to select :shippingMethod shipping method')]
    public function iShouldNotBeAbleToSelectShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->iTryToSelectShippingMethod($shippingMethod);

        Assert::same($this->client->getLastResponse()->getStatusCode(), 422);
        Assert::true($this->responseChecker->isViolationWithMessageInResponse($this->client->getLastResponse(), sprintf(
            'The shipping method %s is not available for this order. Please reselect your shipping method.',
            $shippingMethod->getName(),
        )));
    }

    #[Then('I should see that this shipping method is not available for this address')]
    public function iShouldSeeThatThisShippingMethodIsNotAvailableForThisAddress(): void
    {
        Assert::true(
            $this->responseChecker->isViolationWithMessageInResponse(
                $this->client->getLastResponse(),
                'The shipping method DHL is not available for this order. Please reselect your shipping method.',
            ),
        );
    }

    #[Then('I should be informed that shipping method with code :code does not exist')]
    public function iShouldBeInformedThatShippingMethodWithCodeDoesNotExist(string $code): void
    {
        Assert::true($this->responseChecker->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            sprintf('The shipping method with %s code does not exist.', $code),
        ));
    }
}
