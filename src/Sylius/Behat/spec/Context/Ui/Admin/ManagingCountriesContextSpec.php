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
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\Admin\Country\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ManagingCountriesContextSpec extends ObjectBehavior
{
    function let(IndexPageInterface $countryIndexPage, CreatePageInterface $countryCreatePage)
    {
        $this->beConstructedWith($countryIndexPage, $countryCreatePage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\Admin\ManagingCountriesContext');
    }

    function it_is_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_opens_country_creation_page(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->open()->shouldBeCalled();

        $this->iWantToCreateNewCountry();
    }

    function it_chooses_name_in_creation_form(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->chooseName('France')->shouldBeCalled();

        $this->iChoose('France');
    }

    function it_adds_a_country(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->create()->shouldBeCalled();

        $this->iAddIt();
    }

    function it_asserts_that_successful_message_appears(IndexPageInterface $countryIndexPage)
    {
        $countryIndexPage->hasSuccessMessage()->willReturn(true);
        $countryIndexPage->isSuccessfullyCreated()->willReturn(true);

        $this->iShouldBeNotifiedAboutSuccess();
    }

    function it_throws_not_equal_exception_if_message_is_not_successful(IndexPageInterface $countryIndexPage)
    {
        $countryIndexPage->hasSuccessMessage()->willReturn(false);
        $countryIndexPage->isSuccessfullyCreated()->willReturn(true);

        $this->shouldThrow(NotEqualException::class)->during('iShouldBeNotifiedAboutSuccess');
    }

    function it_throws_not_equal_exception_if_successful_message_does_not_appear(IndexPageInterface $countryIndexPage)
    {
        $countryIndexPage->hasSuccessMessage()->willReturn(true);
        $countryIndexPage->isSuccessfullyCreated()->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('iShouldBeNotifiedAboutSuccess');
    }

    function it_asserts_that_country_appears_in_the_store(IndexPageInterface $countryIndexPage, CountryInterface $country)
    {
        $country->getCode()->willReturn('FR');
        $countryIndexPage->isResourceOnPage(['code' => 'FR'])->willReturn(true);

        $this->countryWithNameShouldAppearInTheStore($country);
    }

    function it_throws_not_equal_exception_if_country_does_not_appear_in_the_store(
        IndexPageInterface $countryIndexPage,
        CountryInterface $country
    ) {
        $country->getCode()->willReturn('FR');
        $countryIndexPage->isResourceOnPage(['code' => 'FR'])->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('countryWithNameShouldAppearInTheStore', [$country]);
    }

    function it_asserts_that_country_name_can_not_be_choosen_again(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->chooseName('France')->willThrow(ElementNotFoundException::class);

        $this->iShouldNotBeAbleToChoose('France');
    }

    function it_thorws_exception_if_country_name_can_be_choosen_again(CreatePageInterface $countryCreatePage)
    {
        $countryCreatePage->chooseName('France')->willThrow(\Exception::class);

        $this->shouldThrow(\Exception::class)->during('iShouldNotBeAbleToChoose', ['France']);
    }
}
