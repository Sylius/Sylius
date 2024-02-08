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

namespace Sylius\Behat\Context\Hybrid\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Context\Setup\ShopSecurityContext;
use Sylius\Behat\Service\SharedStorageInterface;

class SecurityContext implements Context
{
    public function __construct(
        private ShopSecurityContext $uiSecurityContext,
        private ShopSecurityContext $apiSecurityContext,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Given I am a logged in customer on the web store and in the API
     */
    public function IAmALoggedInCustomerOnTheApiAndTheUi(): void
    {
        $this->apiSecurityContext->iAmLoggedInCustomer();
        $this->uiSecurityContext->iAmLoggedInAs($this->sharedStorage->get('user')->getEmail());
    }
}
