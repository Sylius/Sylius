<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\Admin\ManagingCountriesContext;
use Sylius\Behat\Page\Admin\Country\CreatePageInterface;
use Sylius\Behat\Page\Admin\Country\IndexPageInterface;
use Sylius\Behat\Page\Admin\Country\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @mixin ManagingCountriesContext
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ManagingCountriesContextSpec extends ObjectBehavior
{
    function let(
        IndexPageInterface $countryIndexPage,
        CreatePageInterface $countryCreatePage,
        UpdatePageInterface $countryUpdatePage,
        NotificationCheckerInterface $notificationValidator
    ) {
        $this->beConstructedWith(
            $countryIndexPage,
            $countryCreatePage,
            $countryUpdatePage,
            $notificationValidator
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\Admin\ManagingCountriesContext');
    }

    function it_is_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_opens_country_create_page(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->open()->shouldBeCalled();

        $this->iWantToAddNewCountry();
    }

    function it_chooses_name_in_creation_form(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->chooseNameProperly('France')->shouldBeCalled();

        $this->iChoose('France');
    }

    function it_adds_a_country(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->create()->shouldBeCalled();

        $this->iAddIt();
    }

    function it_asserts_that_successful_creation_message_appears(NotificationCheckerInterface $notificationValidator)
    {
        $notificationValidator->checkCreationNotification('country')->shouldBeCalled();

        $this->iShouldBeNotifiedAboutSuccessfulCreation();
    }

    function it_asserts_that_successful_edition_message_appears(NotificationCheckerInterface $notificationValidator)
    {
        $notificationValidator->checkEditionNotification('country')->shouldBeCalled();

        $this->iShouldBeNotifiedAboutSuccessfulEdition();
    }

    function it_asserts_that_country_appears_in_the_store(UpdatePageInterface $countryUpdatePage, CountryInterface $country)
    {
        $country->getId()->willReturn(1);
        $country->getCode()->willReturn('UK');
        $countryUpdatePage->isOpen(['id' => 1])->willReturn(true);

        $this->countryShouldAppearInTheStore($country);
    }

    function it_throws_an_exception_if_country_does_not_appear_in_the_store(
        UpdatePageInterface $countryUpdatePage,
        CountryInterface $country
    ) {
        $country->getId()->willReturn(1);
        $country->getCode()->willReturn('UK');
        $countryUpdatePage->isOpen(['id' => 1])->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('countryShouldAppearInTheStore', [$country]);
    }

    function it_opens_country_update_page(UpdatePageInterface $countryUpdatePage, CountryInterface $country)
    {
        $country->getId()->willReturn(10);
        $countryUpdatePage->open(['id' => 10])->shouldBeCalled();

        $this->iWantToEditThisCountry($country);
    }

    function it_disables_country(UpdatePageInterface $countryUpdatePage)
    {
        $countryUpdatePage->disable()->shouldBeCalled();

        $this->iDisableIt();
    }

    function it_enables_country(UpdatePageInterface $countryUpdatePage)
    {
        $countryUpdatePage->enable()->shouldBeCalled();

        $this->iEnableIt();
    }

    function it_saves_changes(UpdatePageInterface $countryUpdatePage)
    {
        $countryUpdatePage->saveChanges()->shouldBeCalled();

        $this->iSaveMyChanges();
    }

    function it_asserts_that_country_is_disabled(IndexPageInterface $countryIndexPage, CountryInterface $country)
    {
        $countryIndexPage->open()->shouldBeCalled();
        $countryIndexPage->isCountryDisabled($country)->willReturn(true);
        $this->thisCountryShouldBeDisabled($country);
    }

    function it_asserts_that_country_is_enabled(IndexPageInterface $countryIndexPage, CountryInterface $country)
    {
        $countryIndexPage->open()->shouldBeCalled();
        $countryIndexPage->isCountryEnabled($country)->willReturn(true);
        $this->thisCountryShouldBeEnabled($country);
    }

    function it_throws_an_exception_if_country_has_not_proper_status(IndexPageInterface $countryIndexPage, CountryInterface $country)
    {
        $countryIndexPage->open()->shouldBeCalled();

        $countryIndexPage->isCountryDisabled($country)->willReturn(false);
        $countryIndexPage->isCountryEnabled($country)->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('thisCountryShouldBeEnabled', [$country]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('thisCountryShouldBeDisabled', [$country]);
    }

    function it_asserts_that_country_name_can_not_be_choosen_again(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->chooseName('France')->willThrow(ElementNotFoundException::class);

        $this->iShouldNotBeAbleToChoose('France');
    }

    function it_throws_an_exception_if_country_name_can_be_chosen_again(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->chooseName('France')->willThrow(\Exception::class);

        $this->shouldThrow(\Exception::class)->during('iShouldNotBeAbleToChoose', ['France']);
    }

    function it_asserts_that_country_code_field_is_disabled(UpdatePageInterface $countryUpdatePage)
    {
        $countryUpdatePage->isCodeFieldDisabled()->willReturn(true);
        $this->theCodeFieldShouldBeDisabled();
    }

    function it_throws_not_equal_exception_if_country_code_field_is_not_disabled(UpdatePageInterface $countryUpdatePage)
    {
        $countryUpdatePage->isCodeFieldDisabled()->willReturn(false);
        $this->shouldThrow(\InvalidArgumentException::class)->during('theCodeFieldShouldBeDisabled');
    }

    function it_asserts_that_a_country_has_the_province(UpdatePageInterface $countryUpdatePage)
    {
        $countryUpdatePage->isThereProvince('Scotland')->willReturn(true);

        $this->countryShouldHaveProvince('Scotland');
    }

    function it_throws_an_exception_if_a_country_does_not_have_the_province(
        UpdatePageInterface $countryUpdatePage
    ) {
        $countryUpdatePage->isThereProvince('Scotland')->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('countryShouldHaveProvince', ['Scotland']);
    }
}
