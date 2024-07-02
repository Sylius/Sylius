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

namespace spec\Sylius\Bundle\CoreBundle\Twig\ErrorRenderer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinder as AdminErrorTemplateFinder;
use Sylius\Bundle\ShopBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinder as ShopErrorTemplateFinder;
use Symfony\Bridge\Twig\ErrorRenderer\TwigErrorRenderer as DecoratedTwigErrorRenderer;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class TwigErrorRendererSpec extends ObjectBehavior
{
    function let(
        DecoratedTwigErrorRenderer $decoratedTwigErrorRenderer,
        Environment $twig,
        ShopErrorTemplateFinder $shopTemplateFinder,
        AdminErrorTemplateFinder $adminTemplateFinder,
    ): void {
        $this->beConstructedWith($decoratedTwigErrorRenderer, $twig, [$shopTemplateFinder, $adminTemplateFinder], false);
    }

    function it_implements_error_renderer_interface()
    {
        $this->shouldImplement(ErrorRendererInterface::class);
    }

    function it_renders_using_decorated_renderer_if_debug(
        DecoratedTwigErrorRenderer $decoratedTwigErrorRenderer,
        Environment $twig,
        \Throwable $exception,
        FlattenException $flattenException,
    ): void {
        $this->beConstructedWith($decoratedTwigErrorRenderer, $twig, [], true);
        $flattenException->getStatusCode()->willReturn(500);
        $decoratedTwigErrorRenderer->render($exception)->willReturn($flattenException);

        $this->render($exception)->shouldReturn($flattenException);
    }

    function it_renders_using_decorated_renderer_if_no_templates_found(
        DecoratedTwigErrorRenderer $decoratedTwigErrorRenderer,
        Environment $twig,
        AdminErrorTemplateFinder $adminTemplateFinder,
        ShopErrorTemplateFinder $shopTemplateFinder,
    ): void {
        $exception = new NotFoundHttpException('Not Found', null, 404, []);
        $templateName = '@Twig/Exception/Admin/error404.html.twig';
        $fallbackTemplateName = '@Twig/Exception/Admin/error.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);
        $adminTemplateFinder->findTemplate(404)->willReturn(null);
        $shopTemplateFinder->findTemplate(404)->willReturn(null);

        $decoratedTwigErrorRenderer->render($exception)->willReturn($flattenException);

        $twig->render($templateName, [
            'exception' => $flattenException,
            'status_code' => 404,
            'status_text' => $flattenException->getStatusText(),
        ])->shouldNotBeCalled();

        $twig->render($fallbackTemplateName, [
            'exception' => $flattenException,
            'status_code' => 404,
            'status_text' => $flattenException->getStatusText(),
        ])->shouldNotBeCalled();

        $this->render($exception)->shouldReturn($flattenException);
    }

    function it_renders_using_custom_template_for_admin_if_exists_and_in_admin_section(
        Environment $twig,
        AdminErrorTemplateFinder $adminTemplateFinder,
    ): void {
        $exception = new NotFoundHttpException('Not Found', null, 404, []);
        $templateName = '@Twig/Exception/Admin/error404.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);
        $adminTemplateFinder->findTemplate(404)->willReturn($templateName);

        $twig->render($templateName, [
            'exception' => $flattenException,
            'status_code' => 404,
            'status_text' => $flattenException->getStatusText(),
        ])->shouldBeCalled()->willReturn('test');

        $this->render($exception)->getAsString()->shouldReturn('test');
    }

    function it_renders_custom_fallback_template_for_admin_if_dedicated_for_the_exception_does_not_exists_and_in_admin_section(
        Environment $twig,
        AdminErrorTemplateFinder $adminTemplateFinder,
    ): void {
        $exception = new HttpException(422, 'Error', null);
        $templateName = '@Twig/Exception/Admin/error422.html.twig';
        $fallbackTemplateName = '@Twig/Exception/Admin/error.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);

        $adminTemplateFinder->findTemplate(422)->willReturn($fallbackTemplateName);

        $twig->render($templateName, [
            'exception' => $flattenException,
            'status_code' => 422,
            'status_text' => $flattenException->getStatusText(),
        ])->shouldNotBeCalled();

        $twig->render($fallbackTemplateName, [
            'exception' => $flattenException,
            'status_code' => 422,
            'status_text' => $flattenException->getStatusText(),
        ])->shouldBeCalled()->willReturn('test');

        $this->render($exception)->getAsString()->shouldReturn('test');
    }

    function it_renders_custom_fallback_template_for_shop_if_dedicated_for_the_exception_does_not_exists_and_not_in_admin_section(
        Environment $twig,
        ShopErrorTemplateFinder $shopTemplateFinder,
    ): void {
        $exception = new HttpException(422, 'Error', null);
        $templateName = '@Twig/Exception/Shop/error422.html.twig';
        $fallbackTemplateName = '@Twig/Exception/Shop/error.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);

        $shopTemplateFinder->findTemplate(422)->willReturn($fallbackTemplateName);

        $twig->render($templateName, [
            'exception' => $flattenException,
            'status_code' => 422,
            'status_text' => $flattenException->getStatusText(),
        ])->shouldNotBeCalled();

        $twig->render($fallbackTemplateName, [
            'exception' => $flattenException,
            'status_code' => 422,
            'status_text' => $flattenException->getStatusText(),
        ])->shouldBeCalled()->willReturn('test');

        $this->render($exception)->getAsString()->shouldReturn('test');
    }

    function it_renders_using_custom_template_for_shop_if_exists_and_not_in_admin_section(
        Environment $twig,
        ShopErrorTemplateFinder $shopTemplateFinder,
    ): void {
        $exception = new NotFoundHttpException('Not Found', null, 404, []);
        $templateName = '@Twig/Exception/Shop/error404.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);

        $shopTemplateFinder->findTemplate(404)->willReturn($templateName);

        $twig->render($templateName, [
            'exception' => $flattenException,
            'status_code' => 404,
            'status_text' => $flattenException->getStatusText(),
        ])->shouldBeCalled()->willReturn('test');

        $this->render($exception)->getAsString()->shouldReturn('test');
    }
}
