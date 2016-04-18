<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Service;

use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Service\CurrentPageResolver;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * @mixin CurrentPageResolver
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CurrentPageResolverSpec extends ObjectBehavior
{
    function let(Session $session, UrlMatcherInterface $urlMatcher)
    {
        $this->beConstructedWith($session, $urlMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Service\CurrentPageResolver');
    }

    function it_implements_current_page_resolver_interface()
    {
        $this->shouldImplement(CurrentPageResolverInterface::class);
    }

    function it_returns_create_page_if_current_route_name_contains_create_word(
        Session $session,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        UrlMatcherInterface $urlMatcher
    ) {
        $session->getCurrentUrl()->willReturn('https://sylius.com/resource/new');
        $urlMatcher->match('/resource/new')->willReturn(['_route' => 'sylius_resource_create']);

        $this->getCurrentPageWithForm($createPage, $updatePage)->shouldReturn($createPage);
    }

    function it_returns_update_page_if_current_route_name_contains_update_word(
        Session $session,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        UrlMatcherInterface $urlMatcher
    ) {
        $session->getCurrentUrl()->willReturn('https://sylius.com/resource/edit');
        $urlMatcher->match('/resource/edit')->willReturn(['_route' => 'sylius_resource_update']);

        $this->getCurrentPageWithForm($createPage, $updatePage)->shouldReturn($updatePage);
    }

    function it_throws_an_exception_if_neither_create_nor_update_key_word_has_been_found(
        Session $session,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        UrlMatcherInterface $urlMatcher
    ) {
        $session->getCurrentUrl()->willReturn('https://sylius.com/resource/show');
        $urlMatcher->match('/resource/show')->willReturn(['_route' => 'sylius_resource_show']);

        $this->shouldThrow(\LogicException::class)->during('getCurrentPageWithForm', [$createPage, $updatePage]);
    }
}
