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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Webmozart\Assert\Assert;

final class ManagingAdministratorsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(ApiClientInterface $client, ResponseCheckerInterface $responseChecker)
    {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I browse administrators
     * @When I want to browse administrators
     */
    public function iBrowseAdministrators(): void
    {
        $this->client->index();
    }

    /**
     * @Then I should see a single administrator in the list
     * @Then there should be :count administrators in the list
     */
    public function iShouldSeeAdministratorsInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then the administrator :email should appear in the store
     * @Then I should see the administrator :email in the list
     */
    public function theAdministratorShouldAppearInTheStore(string $email): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'email', $email),
            sprintf('Administrator with email %s does not exist', $email)
        );
    }
}
