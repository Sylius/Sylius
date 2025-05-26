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

namespace Tests\Sylius\Bundle\UiBundle\Twig\ErrorRenderer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\Twig\ErrorRenderer\TwigErrorRenderer;
use Sylius\Bundle\UiBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Symfony\Bridge\Twig\ErrorRenderer\TwigErrorRenderer as DecoratedTwigErrorRenderer;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class TwigErrorRendererTest extends TestCase
{
    private DecoratedTwigErrorRenderer&MockObject $decoratedTwigErrorRenderer;

    private Environment&MockObject $twig;

    private ErrorTemplateFinderInterface&MockObject $shopTemplateFinder;

    private ErrorTemplateFinderInterface&MockObject $adminTemplateFinder;

    private TwigErrorRenderer $twigErrorRenderer;

    protected function setUp(): void
    {
        $this->decoratedTwigErrorRenderer = $this->createMock(DecoratedTwigErrorRenderer::class);
        $this->twig = $this->createMock(Environment::class);
        $this->shopTemplateFinder = $this->createMock(ErrorTemplateFinderInterface::class);
        $this->adminTemplateFinder = $this->createMock(ErrorTemplateFinderInterface::class);
        $this->twigErrorRenderer = new TwigErrorRenderer(
            $this->decoratedTwigErrorRenderer,
            $this->twig,
            [$this->shopTemplateFinder, $this->adminTemplateFinder],
            false,
        );
    }

    public function testImplementsErrorRendererInterface(): void
    {
        $this->assertInstanceOf(ErrorRendererInterface::class, $this->twigErrorRenderer);
    }

    public function testRendersUsingDecoratedRendererIfDebug(): void
    {
        /** @var \Throwable&MockObject $exception */
        $exception = $this->createMock(\Throwable::class);
        /** @var FlattenException&MockObject $flattenException */
        $flattenException = $this->createMock(FlattenException::class);

        $this->twigErrorRenderer = new TwigErrorRenderer($this->decoratedTwigErrorRenderer, $this->twig, [], true);
        $flattenException->expects($this->never())->method('getStatusCode')->willReturn(500);
        $this->decoratedTwigErrorRenderer
            ->expects($this->once())
            ->method('render')
            ->with($exception)
            ->willReturn($flattenException)
        ;

        $this->assertSame($flattenException, $this->twigErrorRenderer->render($exception));
    }

    public function testRendersUsingDecoratedRendererIfNoTemplatesFound(): void
    {
        $exception = new NotFoundHttpException('Not Found', null, 404, []);
        $templateName = '@Twig/Exception/Admin/error404.html.twig';
        $fallbackTemplateName = '@Twig/Exception/Admin/error.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);
        $this->adminTemplateFinder->expects($this->once())->method('findTemplate')->with(404)->willReturn(null);
        $this->decoratedTwigErrorRenderer
            ->expects($this->once())
            ->method('render')
            ->with($exception)
            ->willReturn($flattenException)
        ;
        $this->twig->expects($this->never())->method('render')->with($templateName, [
            'exception' => $flattenException,
            'status_code' => 404,
            'status_text' => $flattenException->getStatusText(),
        ]);
        $this->twig->expects($this->never())->method('render')->with($fallbackTemplateName, [
            'exception' => $flattenException,
            'status_code' => 404,
            'status_text' => $flattenException->getStatusText(),
        ]);

        $this->assertSame($flattenException, $this->twigErrorRenderer->render($exception));
    }

    public function testRendersUsingCustomTemplateForAdminIfExistsAndInAdminSection(): void
    {
        $exception = new NotFoundHttpException('Not Found', null, 404, []);
        $templateName = '@Twig/Exception/Admin/error404.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);
        $this->adminTemplateFinder->expects($this->once())->method('findTemplate')->with(404)->willReturn($templateName);
        $this->twig->expects($this->once())->method('render')->with($templateName, [
            'exception' => $flattenException,
            'status_code' => 404,
            'status_text' => $flattenException->getStatusText(),
        ])->willReturn('test');

        $this->assertSame('test', $this->twigErrorRenderer->render($exception)->getAsString());
    }

    public function testRendersCustomFallbackTemplateForAdminIfDedicatedForTheExceptionDoesNotExistsAndInAdminSection(): void
    {
        $exception = new HttpException(422, 'Error', null);
        $fallbackTemplateName = '@Twig/Exception/Admin/error.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);
        $this->adminTemplateFinder->expects($this->once())->method('findTemplate')->with(422)->willReturn($fallbackTemplateName);
        $this->twig->expects($this->once())->method('render')->with($fallbackTemplateName, [
            'exception' => $flattenException,
            'status_code' => 422,
            'status_text' => $flattenException->getStatusText(),
        ])->willReturn('test');

        $this->assertSame('test', $this->twigErrorRenderer->render($exception)->getAsString());
    }

    public function testRendersCustomFallbackTemplateForShopIfDedicatedForTheExceptionDoesNotExistsAndNotInAdminSection(): void
    {
        $exception = new HttpException(422, 'Error', null);
        $fallbackTemplateName = '@Twig/Exception/Shop/error.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);
        $this->shopTemplateFinder->expects($this->once())->method('findTemplate')->with(422)->willReturn($fallbackTemplateName);
        $this->twig->expects($this->once())->method('render')->with($fallbackTemplateName, [
            'exception' => $flattenException,
            'status_code' => 422,
            'status_text' => $flattenException->getStatusText(),
        ])->willReturn('test');

        $this->assertSame('test', $this->twigErrorRenderer->render($exception)->getAsString());
    }

    public function testRendersUsingCustomTemplateForShopIfExistsAndNotInAdminSection(): void
    {
        $exception = new NotFoundHttpException('Not Found', null, 404, []);
        $templateName = '@Twig/Exception/Shop/error404.html.twig';
        $flattenException = FlattenException::createFromThrowable($exception);
        $this->shopTemplateFinder->expects($this->once())->method('findTemplate')->with(404)->willReturn($templateName);
        $this->twig->expects($this->once())->method('render')->with($templateName, [
            'exception' => $flattenException,
            'status_code' => 404,
            'status_text' => $flattenException->getStatusText(),
        ])->willReturn('test');

        $this->assertSame('test', $this->twigErrorRenderer->render($exception)->getAsString());
    }
}
