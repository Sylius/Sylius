<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CountryContextSpec extends ObjectBehavior
{
    function let(SharedStorageInterface $sharedStorage, IndexPageInterface $adminCountryIndexPage, CreatePageInterface $adminCountryCreatePage)
    {
        $this->beConstructedWith($sharedStorage, $adminCountryIndexPage, $adminCountryCreatePage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\CountryContext');
    }

    function it_is_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_opens_country_creation_page($adminCountryCreatePage)
    {
        $adminCountryCreatePage->open()->shouldBeCalled();

        $this->iWantToCreateNewCountry();
    }

    function it_fills_name_in_creation_form_and_save_it_in_shared_storage($sharedStorage, $adminCountryCreatePage)
    {
        $sharedStorage->set('countryName', 'France')->shouldBeCalled();
        $adminCountryCreatePage->fillName('France')->shouldBeCalled();

        $this->iNameIt('France');
    }

    function it_press_create_button_on_creation_page($adminCountryCreatePage)
    {
        $adminCountryCreatePage->create()->shouldBeCalled();

        $this->iAddIt();
    }

    function it_asserts_that_successful_message_appears($adminCountryIndexPage)
    {
        $adminCountryIndexPage->isSuccessfulMessage()->willReturn(true);
        $adminCountryIndexPage->isSuccessfullyCreated()->willReturn(true);

        $this->iShouldBeNotifiedAboutSuccess();
    }

    function it_asserts_that_country_appears_in_the_store($sharedStorage, $adminCountryIndexPage)
    {
        $sharedStorage->get('countryName')->willReturn('France');
        $adminCountryIndexPage->isResourceAppearInTheStoreBy(['name' => 'France'])->willReturn(true);

        $this->thisCountryShouldAppearInTheStore();
    }
}
