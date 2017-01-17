<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Service\Resolver;

use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolver;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CurrentPageResolverSpec extends ObjectBehavior
{
    function let(Session $session, UrlMatcherInterface $urlMatcher)
    {
        $this->beConstructedWith($session, $urlMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CurrentPageResolver::class);
    }

    function it_implements_current_page_resolver_interface()
    {
        $this->shouldImplement(CurrentPageResolverInterface::class);
    }

    function it_returns_current_page_based_on_matched_route(
        Session $session,
        SymfonyPageInterface $createPage,
        SymfonyPageInterface $updatePage,
        UrlMatcherInterface $urlMatcher
    ) {
        $session->getCurrentUrl()->willReturn('https://sylius.com/resource/new');
        $urlMatcher->match('/resource/new')->willReturn(['_route' => 'sylius_resource_create']);

        $createPage->getRouteName()->willReturn('sylius_resource_create');
        $updatePage->getRouteName()->willReturn('sylius_resource_update');

        $this->getCurrentPageWithForm([$createPage, $updatePage])->shouldReturn($createPage);
    }

    function it_throws_an_exception_if_neither_create_nor_update_key_word_has_been_found(
        Session $session,
        SymfonyPageInterface $createPage,
        SymfonyPageInterface $updatePage,
        UrlMatcherInterface $urlMatcher
    ) {
        $session->getCurrentUrl()->willReturn('https://sylius.com/resource/show');
        $urlMatcher->match('/resource/show')->willReturn(['_route' => 'sylius_resource_show']);

        $createPage->getRouteName()->willReturn('sylius_resource_create');
        $updatePage->getRouteName()->willReturn('sylius_resource_update');

        $this->shouldThrow(\LogicException::class)->during('getCurrentPageWithForm', [[$createPage, $updatePage]]);
    }
}
