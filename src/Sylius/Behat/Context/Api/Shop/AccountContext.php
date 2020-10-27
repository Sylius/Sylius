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
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class AccountContext implements Context
{
    /** @var ApiClientInterface */
    private $shopUserClient;

    /** @var Request */
    private $request;

    public function __construct(ApiClientInterface $shopUserClient)
    {
        $this->shopUserClient = $shopUserClient;
    }

    /**
     * @When I change password from :oldPassword to :newPassword
     */
    public function iChangePasswordTo(string $oldPassword, string $newPassword): void
    {
        $this->request->updateContent([
            'oldPassword' => $oldPassword,
            'password' => $newPassword,
            'confirmPassword' => $newPassword
        ]);
    }

    /**
     * @When I want to change my password
     */
    public function iWantToChangeMyPassword(): void
    {
        $this->request = Request::customUpdate('shop', null , null, 'change-password');
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->shopUserClient->executeCustomRequest($this->request);
    }

    /**
     * @Then I should be notified that my password has been successfully changed
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyChanged(): void
    {
        Assert::same($this->shopUserClient->getLastResponse()->getStatusCode(), 204);
    }
}
