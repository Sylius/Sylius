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

namespace spec\Sylius\Bundle\AdminBundle\Twig\Ux;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\UX\TwigComponent\ComponentTemplateFinderInterface;
use Twig\Loader\LoaderInterface;

final class ComponentTemplateFinderSpec extends ObjectBehavior
{
    function let(ComponentTemplateFinderInterface $decorated, LoaderInterface $loader): void
    {
        $this->beConstructedWith($decorated, $loader);
    }

    function it_returns_guessed_path_based_on_anonymous_component_name_starting_with_sylius_admin(
        ComponentTemplateFinderInterface $decorated,
        LoaderInterface $loader,
    ): void {
        $decorated->findAnonymousComponentTemplate(Argument::any())->shouldNotBeCalled();
        $loader->exists('@SyliusAdmin/shared/components/some_component.html.twig')->willReturn(true);

        $this->findAnonymousComponentTemplate('sylius_admin:some_component')->shouldReturn('@SyliusAdmin/shared/components/some_component.html.twig');
    }

    function it_returns_guessed_path_based_on_anonymous_component_with_complext_name_starting_with_sylius_admin(
        ComponentTemplateFinderInterface $decorated,
        LoaderInterface $loader,
    ): void {
        $decorated->findAnonymousComponentTemplate(Argument::any())->shouldNotBeCalled();
        $loader->exists('@SyliusAdmin/shared/components/some_component/some_sub_component.html.twig')->willReturn(true);

        $this
            ->findAnonymousComponentTemplate('sylius_admin:some_component:some_sub_component')
            ->shouldReturn('@SyliusAdmin/shared/components/some_component/some_sub_component.html.twig')
        ;
    }

    function it_does_nothing_when_passed_anonymous_component_name_is_not_starting_with_sylius_admin(
        ComponentTemplateFinderInterface $decorated,
    ): void {
        $decorated->findAnonymousComponentTemplate('some_component')->willReturn('some_component_template.html.twig');

        $this->findAnonymousComponentTemplate('some_component')->shouldReturn('some_component_template.html.twig');
    }

    function it_returns_null_when_guessed_template_does_not_exist(
        ComponentTemplateFinderInterface $decorated,
        LoaderInterface $loader,
    ): void {
        $decorated->findAnonymousComponentTemplate(Argument::any())->shouldNotBeCalled();
        $loader->exists('@SyliusAdmin/shared/components/some_component.html.twig')->willReturn(false);

        $this->findAnonymousComponentTemplate('sylius_admin:some_component')->shouldReturn(null);
    }
}
