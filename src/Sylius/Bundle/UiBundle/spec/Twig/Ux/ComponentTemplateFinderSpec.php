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

namespace spec\Sylius\Bundle\UiBundle\Twig\Ux;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\UX\TwigComponent\ComponentTemplateFinderInterface;
use Twig\Loader\LoaderInterface;

final class ComponentTemplateFinderSpec extends ObjectBehavior
{
    function let(ComponentTemplateFinderInterface $decorated, LoaderInterface $loader): void
    {
        $this->beConstructedWith(
            $decorated,
            $loader,
            [
                'sylius_ui' => '@SyliusUi/components',
                'sylius_ui_shop' => '@SyliusUi/shop/components',
            ],
        );
    }

    function it_calls_decorated_finder_if_no_prefix_matches(
        ComponentTemplateFinderInterface $decorated,
        LoaderInterface $loader,
    ): void {
        $decorated->findAnonymousComponentTemplate('sylius_ui_admin:component')->willReturn('ui_admin/component.html.twig');

        $this->findAnonymousComponentTemplate('sylius_ui_admin:component')->shouldReturn('ui_admin/component.html.twig');

        $loader->exists(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_finds_anonymous_component_template(
        ComponentTemplateFinderInterface $decorated,
        LoaderInterface $loader,
    ): void {
        $loader->exists('@SyliusUi/shop/components/some_component/some_sub_component.html.twig')->willReturn(true);

        $this->findAnonymousComponentTemplate('sylius_ui_shop:some_component:some_sub_component')->shouldReturn('@SyliusUi/shop/components/some_component/some_sub_component.html.twig');

        $decorated->findAnonymousComponentTemplate(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_returns_null_if_template_does_not_exist(
        ComponentTemplateFinderInterface $decorated,
        LoaderInterface $loader,
    ): void {
        $loader->exists('@SyliusUi/shop/components/some_component.html.twig')->willReturn(false);

        $this->findAnonymousComponentTemplate('sylius_ui_shop:some_component')->shouldReturn(null);

        $decorated->findAnonymousComponentTemplate(Argument::any())->shouldNotHaveBeenCalled();
    }
}
