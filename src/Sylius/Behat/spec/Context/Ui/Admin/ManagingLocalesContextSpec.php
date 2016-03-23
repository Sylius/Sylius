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
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\Admin\ManagingLocalesContext;
use Sylius\Behat\Page\Admin\Locale\CreatePageInterface;
use Sylius\Behat\Page\Admin\Locale\IndexPageInterface;
use Sylius\Behat\Page\Admin\Locale\UpdatePageInterface;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * @mixin ManagingLocalesContext
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ManagingLocalesContextSpec extends ObjectBehavior
{
    function let(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        NotificationAccessorInterface $notificationAccessor,
        UpdatePageInterface $updatePage
    ) {
        $this->beConstructedWith($createPage, $indexPage, $notificationAccessor, $updatePage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\Admin\ManagingLocalesContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_opens_locale_creation_page(CreatePageInterface $createPage)
    {
        $createPage->open()->shouldBeCalled();

        $this->iWantToCreateNewLocale();
    }

    function it_opens_locale_update_page(UpdatePageInterface $updatePage, LocaleInterface $locale)
    {
        $locale->getId()->willReturn(1);
        $updatePage->open(['id' => 1])->shouldBeCalled();

        $this->iWantToEditThisLocale($locale);
    }

    function it_disables_locale(UpdatePageInterface $updatePage)
    {
        $updatePage->disable()->shouldBeCalled();

        $this->iDisableIt();
    }

    function it_enables_locale(UpdatePageInterface $updatePage)
    {
        $updatePage->enable()->shouldBeCalled();

        $this->iEnableIt();
    }

    function it_chooses_locale_name(CreatePageInterface $createPage)
    {
        $createPage->chooseName('Norwegian')->shouldBeCalled();

        $this->iChoose('Norwegian');
    }

    function it_creates_a_resource(CreatePageInterface $createPage)
    {
        $createPage->create()->shouldBeCalled();

        $this->iAdd();
    }

    function it_saves_changes(UpdatePageInterface $updatePage)
    {
        $updatePage->saveChanges()->shouldBeCalled();

        $this->iSaveMyChanges();
    }

    function it_asserts_that_resource_was_successfully_created(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->isSuccessfullyCreatedFor(ManagingLocalesContext::RESOURCE_NAME)->willReturn(true);

        $this->iShouldBeNotifiedAboutSuccessfulCreation();
    }

    function it_asserts_that_locale_is_disabled(IndexPageInterface $indexPage, LocaleInterface $locale)
    {
        $indexPage->isLocaleDisabled($locale)->willReturn(true);

        $this->thisLocaleShouldBeDisabled($locale);
    }

    function it_asserts_that_locale_is_enabled(IndexPageInterface $indexPage, LocaleInterface $locale)
    {
        $indexPage->isLocaleEnabled($locale)->willReturn(true);

        $this->thisLocaleShouldBeEnabled($locale);
    }

    function it_throws_not_equal_exception_if_locale_has_not_proper_status(IndexPageInterface $indexPage, LocaleInterface $locale)
    {
        $indexPage->isLocaleDisabled($locale)->willReturn(false);
        $indexPage->isLocaleEnabled($locale)->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('thisLocaleShouldBeDisabled', [$locale]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('thisLocaleShouldBeEnabled', [$locale]);
    }

    function it_throws_an_exception_if_there_is_no_success_message(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(false);
        $notificationAccessor->isSuccessfullyCreatedFor(ManagingLocalesContext::RESOURCE_NAME)->willReturn(true);

        $this->shouldThrow(\InvalidArgumentException::class)->during('iShouldBeNotifiedAboutSuccessfulCreation');
    }

    function it_throws_an_exception_if_resource_was_not_successfully_created(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->isSuccessfullyCreatedFor(ManagingLocalesContext::RESOURCE_NAME)->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('iShouldBeNotifiedAboutSuccessfulCreation');
    }

    function it_asserts_if_store_is_available_in_given_language(IndexPageInterface $indexPage)
    {
        $indexPage->isResourceOnPage(['name' => 'Norwegian'])->willReturn(true);

        $this->storeShouldBeAvailableInLanguage('Norwegian');
    }

    function it_throws_an_exception_if_resource_can_not_be_founded_on_page(IndexPageInterface $indexPage)
    {
        $indexPage->isResourceOnPage(['name' => 'Norwegian'])->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('storeShouldBeAvailableInLanguage', ['Norwegian']);
    }
}
