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
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Webmozart\Assert\Assert;

final class ManagingProvincesContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When /^I want to edit (this province)$/
     */
    public function iWantToEditThisProvince(ProvinceInterface $province): void
    {
        $this->client->buildUpdateRequest($province->getCode());
    }

    /**
     * @When I remove its name
     */
    public function iRemoveItsName(): void
    {
        $this->client->addRequestData('name', '');
    }

    /**
     * @When I try to save this changes
     */
    public function iTryToSaveThisChanges(): void
    {
        $this->client->update();
    }

    /**
     * @Then I should be notified that name can not be empty value
     */
    public function iShouldBeNotifiedThatNameCanNotBeEmptyValue(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Please enter province name.'
        );
    }

    /**
     * @Then /^(this province) should still be named "([^"]+)"$/
     */
    public function theProvinceShouldStillBeNamed(ProvinceInterface $province, string $name): void
    {
        $this->responseChecker->hasValue(
            $this->client->show($province->getCode()),
            'name',
            $name
        );
    }
}
