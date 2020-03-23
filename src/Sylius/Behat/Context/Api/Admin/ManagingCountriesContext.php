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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

final class ManagingCountriesContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SerializerInterface */
    private $serializer;

    /** @var RepositoryInterface */
    private $countryRepository;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SerializerInterface $serializer,
        RepositoryInterface $countryRepository
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->serializer = $serializer;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @When I want to add a new country
     */
    public function iWantToAddANewCountry(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I choose :countryName
     */
    public function iChoose(string $countryName): void
    {
        $this->client->addRequestData('code', $this->getCountryCodeByName($countryName));
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Country could not be created'
        );
    }

    /**
     * @Then the country :countryName should appear in the store
     */
    public function theCountryShouldAppearInTheStore(string $countryName): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $countryName),
            sprintf('There is no country with name "%s"', $countryName)
        );
    }

    /**
     * @Then I should not be able to choose :countryName
     */
    public function iShouldNotBeAbleToChoose(string $countryName): void
    {
        $this->client->addRequestData('code', $this->getCountryCodeByName($countryName));
        $response = $this->client->create();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Country has been created successfully, but it should not'
        );
        Assert::same($this->responseChecker->getError($response), 'code: Country ISO code must be unique.');
    }

    /**
     * @When /^I want to edit (this country)$/
     */
    public function iWantToEditThisCountry(CountryInterface $country): void
    {
        $this->client->buildUpdateRequest($country->getCode());
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->client->addRequestData('enabled', true);
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->client->addRequestData('enabled', false);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Country could not be edited'
        );
    }

    /**
     * @Then /^(this country) should be ([^"]+)$/
     */
    public function thisCountryShouldBeDisabled(CountryInterface $country, string $enabled): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show($country->getCode()),
                'enabled',
                $enabled === 'enabled' ? true : false
            ),
            'Country is not disabled'
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        $countryUpdateSerialised = $this->serializer->serialize(
            $this->countryRepository->findOneBy([]),
            'json', ['groups' => 'country:update']
        );
        Assert::keyNotExists(\json_decode($countryUpdateSerialised, true), 'code');
    }

    private function getCountryCodeByName(string $countryName): string
    {
        $countryList = array_flip(Countries::getNames());
        Assert::keyExists(
            $countryList,
            $countryName,
            sprintf('The country with name "%s" not found', $countryName)
        );

        return $countryList[$countryName];
    }
}
