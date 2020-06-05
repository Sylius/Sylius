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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\HttpFoundation\Response;

final class CartContext implements Context
{
    /** @var ApiClientInterface */
    private $cartsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $cartsClient,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->cartsClient = $cartsClient;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^(cart)$/
     */
    public function provideCart(string $cart): string
    {
        if ($this->sharedStorage->has('cart_token')) {
            $tokenValue = $this->sharedStorage->get('cart_token');

            $response = $this->cartsClient->show($tokenValue);
            if ($response->getStatusCode() === Response::HTTP_OK || $response->getStatusCode() === Response::HTTP_CREATED) {
                return $this->sharedStorage->get('cart_token');
            }
        }
        $response = $this->cartsClient->create(Request::create('orders'));

        $tokenValue = $this->responseChecker->getValue($response, 'tokenValue');
        $this->sharedStorage->set('cart_token', $tokenValue);

        return $tokenValue;
    }
}
