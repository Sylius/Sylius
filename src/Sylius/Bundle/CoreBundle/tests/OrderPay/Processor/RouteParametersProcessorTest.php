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

namespace Tests\Sylius\Bundle\CoreBundle\OrderPay\Processor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OrderPay\Processor\RouteParametersProcessor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class RouteParametersProcessorTest extends TestCase
{
    private ExpressionLanguage&MockObject $expressionLanguage;

    private MockObject&RouterInterface $router;

    private RouteParametersProcessor $processor;

    protected function setUp(): void
    {
        $this->expressionLanguage = $this->createMock(ExpressionLanguage::class);
        $this->router = $this->createMock(RouterInterface::class);

        $this->processor = new RouteParametersProcessor(
            $this->expressionLanguage,
            $this->router,
        );
    }

    public function testProcessesFromARoute(): void
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('a_route', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/a_route')
        ;

        $result = $this->processor->process('a_route');
        $this->assertSame('/a_route', $result);
    }

    public function testProcessesFromARouteAndParameters(): void
    {
        $this->expressionLanguage
            ->expects($this->once())
            ->method('evaluate')
            ->with('value', [])
            ->willReturn('value')
        ;

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('a_route', ['aParam' => 'value'], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/a_route?aParam=value')
        ;

        $result = $this->processor->process('a_route', ['aParam' => 'value']);
        $this->assertSame('/a_route?aParam=value', $result);
    }

    public function testProcessesFromARouteAndParametersAndContext(): void
    {
        $context = ['value' => '1'];

        $this->expressionLanguage
            ->expects($this->once())
            ->method('evaluate')
            ->with('value', $context)
            ->willReturn('1')
        ;

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('a_route', ['aParam' => '1'], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/a_route?aParam=1')
        ;

        $result = $this->processor->process('a_route', ['aParam' => 'value'], UrlGeneratorInterface::ABSOLUTE_PATH, $context);
        $this->assertSame('/a_route?aParam=1', $result);
    }
}
