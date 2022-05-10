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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\Intl\Countries;
use Webmozart\Assert\Assert;

final class ManagingCountriesContext implements Context
{
    private ApiClientInterface $client;

    private ResponseCheckerInterface $responseChecker;

    private SharedStorageInterface $sharedStorage;

    private IriConverterInterface $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @When I want to add a new country
     */
    public function iWantToAddANewCountry(): void
    {
        $this->client->buildCreateRequest(Resources::COUNTRIES);
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
     * @When /^I want to edit (this country)$/
     * @When /^I want to create a new province in (country "([^"]+)")$/
     */
    public function iWantToEditThisCountry(CountryInterface $country): void
    {
        $this->client->buildUpdateRequest(Resources::COUNTRIES, $country->getCode());
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
     * @When I try to save changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @When I name the province :provinceName
     */
    public function iNameTheProvince(string $provinceName): void
    {
        $this->client->addSubResourceData(
            'provinces',
            ['name' => $provinceName]
        );
    }

    /**
     * @When I specify the province code as :provinceCode
     */
    public function iSpecifyTheProvinceCodeAs(string $provinceCode): void
    {
        $this->client->addSubResourceData(
            'provinces',
            ['code' => $provinceCode]
        );
    }

    /**
     * @When I add the :provinceName province with :provinceCode code
     */
    public function iAddTheProvinceWithCode(string $provinceName, string $provinceCode): void
    {
        $this->client->addSubResourceData(
            'provinces',
            ['code' => $provinceCode, 'name' => $provinceName]
        );
    }

    /**
     * @When I add the :name province with :code code and :abbreviation abbreviation
     */
    public function iAddTheProvinceWithCodeAndAbbreviation(string $name, string $code, string $abbreviation): void
    {
        $this->client->addSubResourceData(
            'provinces',
            ['code' => $code, 'name' => $name, 'abbreviation' => $abbreviation]
        );
    }

    /**
     * @When /^I delete the ("[^"]+" province) of (this country)$/
     */
    public function iDeleteTheProvinceOfThisCountry(ProvinceInterface $province, CountryInterface $country): void
    {
        $iri = $this->iriConverter->getItemIriFromResourceClass(get_class($province), ['code' => $province->getCode()]);

        $provinces = $this->responseChecker->getValue($this->client->show(Resources::COUNTRIES, $country->getCode()), 'provinces');
        foreach ($provinces as $countryProvince) {
            if ($iri === $countryProvince) {
                $this->client->removeSubResource('provinces', $countryProvince);
            }
        }
    }

    /**
     * @When I do not specify the province code
     * @When I do not name the province
     */
    public function iDoNotSpecifyTheProvince(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I remove :province province name
     */
    public function iRemoveProvinceName(ProvinceInterface $province): void
    {
        $this->client->buildUpdateRequest(Resources::PROVINCES, $province->getCode());
        $this->client->addRequestData('name', '');
        $this->client->update();
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
     * @Then the country :country should appear in the store
     */
    public function theCountryShouldAppearInTheStore(CountryInterface $country): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::COUNTRIES), 'code', $country->getCode()),
            sprintf('There is no country with name "%s"', $country->getName())
        );
    }

    /**
     * @Then the country :country should have the :province province
     * @Then /^(this country) should have the ("[^"]*" province)$/
     */
    public function theCountryShouldHaveTheProvince(CountryInterface $country, ProvinceInterface $province): void
    {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->subResourceIndex(Resources::COUNTRIES, 'provinces', $country->getCode()),
            'code',
            $province->getCode()
        ));
    }

    /**
     * @Then the province should still be named :province in this country
     */
    public function theProvinceShouldStillBeNamedInThisCountry(ProvinceInterface $province): void
    {
        /** @var CountryInterface $country */
        $country = $this->sharedStorage->get('country');
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->subResourceIndex(Resources::COUNTRIES, 'provinces', $country->getCode()),
            'code',
            $province->getCode()
        ));
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
     * @Then /^(this country) should be (enabled|disabled)$/
     */
    public function thisCountryShouldBeDisabled(CountryInterface $country, string $enabled): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(Resources::COUNTRIES, $country->getCode()),
                'enabled',
                $enabled === 'enabled'
            ),
            'Country is not disabled'
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false($this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'));
    }

    /**
     * @Then /^province with code ("[^"]*") should not be added in (this country)$/
     */
    public function provinceWithCodeShouldNotBeAddedInThisCountry(string $provinceCode, CountryInterface $country): void
    {
        /** @var ProvinceInterface $province */
        foreach ($this->getProvincesOfCountry($country) as $province) {
            Assert::false(
                $province->getCode() === $provinceCode,
                sprintf('The country "%s" should not have the "%s" province', $country->getName(), $province->getName())
            );
        }
    }

    /**
     * @Then this country should not have the :provinceName province
     * @Then province with name :provinceName should not be added in this country
     */
    public function thisCountryShouldNotHaveTheProvince(string $provinceName): void
    {
        /** @var CountryInterface $country */
        $country = $this->sharedStorage->get('country');
        /** @var ProvinceInterface $province */
        foreach ($this->getProvincesOfCountry($country) as $province) {
            Assert::false(
                $province->getName() === $provinceName,
                sprintf('The country "%s" should not have the "%s" province', $country->getName(), $province->getName())
            );
        }
    }

    /**
     * @Then I should be notified that province code must be unique
     */
    public function iShouldBeNotifiedThatProvinceCodeMustBeUnique(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'provinces[1].code: Province code must be unique.'
        );
    }

    /**
     * @Then I should be notified that :field is required
     */
    public function iShouldBeNotifiedThatFieldIsRequired(string $field): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            \sprintf('Please enter province %s.', $field)
        );
    }

    /**
     * @Then I should be notified that name of the province is required
     */
    public function iShouldBeNotifiedThatNameOfTheProvinceIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Please enter province name.'
        );
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

    private function getProvincesOfCountry(CountryInterface $country): iterable
    {
        $response = $this->client->show(Resources::COUNTRIES, $country->getCode());
        $countryFromResponse = $this->responseChecker->getResponseContent($response);

        foreach ($countryFromResponse['provinces'] as $provinceFromResponse) {
            yield $this->iriConverter->getItemFromIri($provinceFromResponse);
        }
    }
}
