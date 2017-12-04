<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\GridBundle\Renderer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\GridBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TwigGridRendererSpec extends ObjectBehavior
{
    function let(
        \Twig_Environment $twig,
        ServiceRegistryInterface $fieldsRegistry,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $formTypeRegistry
    ): void {
        $actionTemplates = [
            'link' => 'SyliusGridBundle:Action:_link.html.twig',
            'form' => 'SyliusGridBundle:Action:_form.html.twig',
        ];
        $filterTemplates = [
            StringFilter::NAME => 'SyliusGridBundle:Filter:_string.html.twig',
        ];

        $this->beConstructedWith(
            $twig,
            $fieldsRegistry,
            $formFactory,
            $formTypeRegistry,
            'SyliusGridBundle:default.html.twig',
            $actionTemplates,
            $filterTemplates
        );
    }

    function it_is_a_grid_renderer(): void
    {
        $this->shouldImplement(GridRendererInterface::class);
    }

    function it_uses_twig_to_render_the_grid_view(\Twig_Environment $twig, GridViewInterface $gridView): void
    {
        $twig->render('SyliusGridBundle:default.html.twig', ['grid' => $gridView])->willReturn('<html>Grid!</html>');
        $this->render($gridView)->shouldReturn('<html>Grid!</html>');
    }

    function it_uses_custom_template_if_specified(\Twig_Environment $twig, GridView $gridView): void
    {
        $twig->render('SyliusGridBundle:custom.html.twig', ['grid' => $gridView])->willReturn('<html>Grid!</html>');
        $this->render($gridView, 'SyliusGridBundle:custom.html.twig')->shouldReturn('<html>Grid!</html>');
    }

    function it_uses_twig_to_render_the_action(\Twig_Environment $twig, GridViewInterface $gridView, Action $action): void
    {
        $action->getType()->willReturn('link');
        $action->getOptions()->willReturn([]);

        $twig
            ->render('SyliusGridBundle:Action:_link.html.twig', [
                'grid' => $gridView,
                'action' => $action,
                'data' => null,
            ])
            ->willReturn('<a href="#">Action!</a>')
        ;

        $this->renderAction($gridView, $action)->shouldReturn('<a href="#">Action!</a>');
    }

    function it_renders_a_field_with_data_via_appropriate_field_type(
        GridViewInterface $gridView,
        Field $field,
        ServiceRegistryInterface $fieldsRegistry,
        FieldTypeInterface $fieldType
    ): void {
        $field->getType()->willReturn('string');
        $fieldsRegistry->get('string')->willReturn($fieldType);
        $fieldType->configureOptions(Argument::type(OptionsResolver::class))
            ->will(function ($args) {
                $args[0]->setRequired('foo');
            })
        ;

        $field->getOptions()->willReturn([
            'foo' => 'bar',
        ]);
        $fieldType->render($field, 'Value', ['foo' => 'bar'])->willReturn('<strong>Value</strong>');

        $this->renderField($gridView, $field, 'Value')->shouldReturn('<strong>Value</strong>');
    }

    function it_throws_an_exception_if_template_is_not_configured_for_given_action_type(
        GridViewInterface $gridView,
        Action $action
    ): void {
        $action->getType()->willReturn('foo');

        $this
            ->shouldThrow(new \InvalidArgumentException('Missing template for action type "foo".'))
            ->during('renderAction', [$gridView, $action])
        ;
    }
}
