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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\Intl\Countries;
use Webmozart\Assert\Assert;

final class ManagingCountriesContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private IriConverterInterface $iriConverter,
    ) {
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
        $this->iSpecifyTheCountryCodeAs($this->getCountryCodeByName($countryName));
    }

    /**
     * @When I specify the country code as :code
     */
    public function iSpecifyTheCountryCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When /^I want to edit (this country)$/
     * @When /^I am editing (this country)$/
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
     * @When I name the province :provinceName
     */
    public function iNameTheProvince(string $provinceName): void
    {
        $this->client->addSubResourceData(
            'provinces',
            ['name' => $provinceName],
        );
    }

    /**
     * @When I specify the province code as :provinceCode
     */
    public function iSpecifyTheProvinceCodeAs(string $provinceCode): void
    {
        $this->client->addSubResourceData(
            'provinces',
            ['code' => $provinceCode],
        );
    }

    /**
     * @When I provide a too long province code
     */
    public function iProvideATooLongProvinceCode(): void
    {
        $this->iSpecifyTheProvinceCodeAs(sprintf('XX-%s', str_repeat('A', $this->getMaxCodeLength())));
    }

    /**
     * @When I add the :provinceName province with :provinceCode code
     */
    public function iAddTheProvinceWithCode(string $provinceName, string $provinceCode): void
    {
        $this->client->addSubResourceData(
            'provinces',
            ['code' => $provinceCode, 'name' => $provinceName],
        );
    }

    /**
     * @When I add the :name province with :code code and :abbreviation abbreviation
     */
    public function iAddTheProvinceWithCodeAndAbbreviation(string $name, string $code, string $abbreviation): void
    {
        $this->client->addSubResourceData(
            'provinces',
            ['code' => $code, 'name' => $name, 'abbreviation' => $abbreviation],
        );
    }

    /**
     * @When /^I(?:| also) delete the ("[^"]+" province) of (this country)$/
     */
    public function iDeleteTheProvinceOfThisCountry(ProvinceInterface $province, CountryInterface $country): void
    {
        $iri = $this->iriConverter->getItemIriFromResourceClass($province::class, ['code' => $province->getCode()]);

        $provinces = $this->responseChecker->getValue($this->client->show(Resources::COUNTRIES, $country->getCode()), 'provinces');
        foreach ($provinces as $countryProvince) {
            if ($iri === $countryProvince) {
                $this->client->removeSubResource('provinces', $countryProvince);
            }
        }
    }

    /**
     * @When I do not specify the country code
     * @When I do not specify the province code
     * @When I do not name the province
     */
    public function iDoNotSpecifyTheField(): void
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
            'Country could not be created',
        );
    }

    /**
     * @Then the country :country should appear in the store
     */
    public function theCountryShouldAppearInTheStore(CountryInterface $country): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::COUNTRIES), 'code', $country->getCode()),
            sprintf('There is no country with name "%s"', $country->getName()),
        );
    }

    /**
     * @Then the country :country should have the :province province
     * @Then /^(this country) should(?:| still) have the ("[^"]*" province)$/
     */
    public function theCountryShouldHaveTheProvince(CountryInterface $country, ProvinceInterface $province): void
    {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->subResourceIndex(Resources::COUNTRIES, 'provinces', $country->getCode()),
            'code',
            $province->getCode(),
        ));
    }

    /**
     * @Then /^(this country) should(?:| still) have the ("[^"]*" and "[^"]*" provinces)$/
     */
    public function theCountryShouldHaveTheProvinceAndProvince(CountryInterface $country, array $provinces): void
    {
        foreach ($provinces as $province) {
            $this->theCountryShouldHaveTheProvince($country, $province);
        }
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
            $province->getCode(),
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
            'Country has been created successfully, but it should not',
        );
        Assert::same($this->responseChecker->getError($response), 'code: Country ISO code must be unique.');
    }

    /**
     * @Then /^(this country) should be (enabled|disabled)$/
     */
    public function thisCountryShouldBe(CountryInterface $country, string $state): void
    {
        $isEnabled = 'enabled' === $state;

        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(Resources::COUNTRIES, $country->getCode()),
                'enabled',
                $isEnabled,
            ),
            sprintf('Country is not %s', $isEnabled ? 'enabled' : 'disabled'),
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
                sprintf('The country "%s" should not have the "%s" province', $country->getName(), $province->getName()),
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
                sprintf('The country "%s" should not have the "%s" province', $country->getName(), $province->getName()),
            );
        }
    }

    /**
     * @Then /^I should be notified that province (code|name) must be unique$/
     */
    public function iShouldBeNotifiedThatProvinceCodeMustBeUnique(string $field): void
    {
        Assert::regex(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('/provinces\[[\d+]\]\.%1$s: Province %1$s must be unique\./', $field),
        );
    }

    /**
     * @Then I should be notified that all province codes and names within this country need to be unique
     */
    public function iShouldBeNotifiedThatAllProvinceCodesAndNamesWithinThisCountryNeedToBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'provinces: All provinces within this country need to have unique codes and names.',
        );
    }

    /**
     * @Then I should be notified that :field is required
     */
    public function iShouldBeNotifiedThatFieldIsRequired(string $field): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            \sprintf('Please enter province %s.', $field),
        );
    }

    /**
     * @Then /^I should be notified that the country code is (required|invalid)$/
     */
    public function iShouldBeNotifiedThatTheCountryCodeIsRequired(string $constraint): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            $constraint === 'required' ? 'Please enter country ISO code.' : 'Country ISO code is invalid.',
        );
    }

    /**
     * @Then I should be notified that name of the province is required
     */
    public function iShouldBeNotifiedThatNameOfTheProvinceIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Please enter province name.',
        );
    }

    /**
     * @Then I should be informed that the provided province code is too long
     */
    public function iShouldBeInformedThatTheProvinceCodeIsTooLong(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The code must not be longer than',
        );
    }

    /**
     * @Then I should be notified that provinces that are in use cannot be deleted
     */
    public function iShouldBeNotifiedThatProvincesThatAreInUseCannotBeDeleted(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete, the province is in use.',
        );
    }

    private function getCountryCodeByName(string $countryName): string
    {
        $countryList = array_flip(Countries::getNames());
        Assert::keyExists(
            $countryList,
            $countryName,
            sprintf('The country with name "%s" not found', $countryName),
        );

        return $countryList[$countryName];
    }

    /** @return iterable<ProvinceInterface> */
    private function getProvincesOfCountry(CountryInterface $country): iterable
    {
        $response = $this->client->show(Resources::COUNTRIES, $country->getCode());
        $countryFromResponse = $this->responseChecker->getResponseContent($response);

        foreach ($countryFromResponse['provinces'] as $provinceFromResponse) {
            yield $this->iriConverter->getResourceFromIri($provinceFromResponse);
        }
    }
}
