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

namespace Sylius\Behat\Context\Api\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;

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
}
