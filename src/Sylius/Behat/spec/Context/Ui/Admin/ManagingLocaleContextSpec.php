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
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\Admin\ManagingLocaleContext;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Locale\CreatePageInterface;

/**
 * @mixin ManagingLocaleContext
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ManagingLocaleContextSpec extends ObjectBehavior
{
    function let(IndexPageInterface $indexPage, CreatePageInterface $createPage)
    {
        $this->beConstructedWith($indexPage, $createPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\Admin\ManagingLocaleContext');
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

    function it_asserts_that_resource_was_successfully_created(IndexPageInterface $indexPage)
    {
        $indexPage->hasSuccessMessage()->willReturn(true);
        $indexPage->isSuccessfullyCreated()->willReturn(true);

        $this->iShouldBeNotifiedAboutSuccess();
    }

    function it_throws_an_exception_if_there_is_no_success_message(IndexPageInterface $indexPage)
    {
        $indexPage->hasSuccessMessage()->willReturn(false);
        $indexPage->isSuccessfullyCreated()->willReturn(true);

        $this->shouldThrow(NotEqualException::class)->during('iShouldBeNotifiedAboutSuccess');
    }

    function it_throws_an_exception_if_resource_was_not_successfully_created(IndexPageInterface $indexPage)
    {
        $indexPage->hasSuccessMessage()->willReturn(true);
        $indexPage->isSuccessfullyCreated()->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('iShouldBeNotifiedAboutSuccess');
    }

    function it_asserts_if_store_is_available_in_given_language(IndexPageInterface $indexPage)
    {
        $indexPage->isResourceOnPage(['name' => 'Norwegian'])->willReturn(true);

        $this->storeShouldBeAvailableInLanguage('Norwegian');
    }

    function it_throws_an_exception_if_resource_can_not_be_founded_on_page(IndexPageInterface $indexPage)
    {
        $indexPage->isResourceOnPage(['name' => 'Norwegian'])->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('storeShouldBeAvailableInLanguage', ['Norwegian']);
    }
}
