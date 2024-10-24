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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final readonly class PromotionContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private SharedStorageInterface $sharedStorage,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I use coupon with code :couponCode
     * @When I remove coupon from my cart
     */
    public function iUseCouponWithCode(?string $couponCode = null): void
    {
        $this->client
            ->buildUpdateRequest(Resources::ORDERS, $this->getCartTokenValue())
            ->setRequestData(['couponCode' => $couponCode])
            ->update()
        ;
    }

    /**
     * @Then I should be notified that the coupon is invalid
     */
    public function iShouldBeNotifiedThatCouponIsInvalid(): void
    {
        $response = $this->client->getLastResponse();

        $availableErrors = [
            'couponCode: Coupon code is invalid.',
            'couponCode: Coupon code has expired.',
            'couponCode: Coupon code is not valid for this order.',
        ];

        Assert::same($response->getStatusCode(), 422);
        Assert::inArray($this->responseChecker->getError($response), $availableErrors);
    }

    private function getCartTokenValue(): string
    {
        if (!$this->sharedStorage->has('cart_token')) {
            throw new \RuntimeException('There is no cart token set in shared storage.');
        }

        return $this->sharedStorage->get('cart_token');
    }
}
