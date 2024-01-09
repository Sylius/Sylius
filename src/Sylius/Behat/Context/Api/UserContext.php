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

namespace Sylius\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Webmozart\Assert\Assert;

final class UserContext implements Context
{
    public function __construct(
        private ApiClientInterface $adminApiClient,
        private SharedStorageInterface $sharedStorage,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @Then the user account should be deleted
     */
    public function accountShouldBeDeleted(): void
    {
        /** @var ShopUserInterface $deletedUser */
        $deletedUser = $this->sharedStorage->get('deleted_user');

        $response = $this->adminApiClient->show(Resources::CUSTOMERS, (string) $deletedUser->getCustomer()->getId());

        Assert::null(
            $this->responseChecker->getValue($response, 'user'),
        );
    }
}
