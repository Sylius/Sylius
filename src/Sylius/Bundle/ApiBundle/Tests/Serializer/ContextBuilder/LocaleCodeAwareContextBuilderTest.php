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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Attribute\LocaleCodeAware;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LocaleCodeAwareContextBuilder;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class LocaleCodeAwareContextBuilderTest extends TestCase
{
    private MockObject&SerializerContextBuilderInterface $decoratedContextBuilder;

    private LocaleContextInterface&MockObject $localeContext;

    private LocaleCodeAwareContextBuilder $localeCodeAwareContextBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedContextBuilder = $this->createMock(SerializerContextBuilderInterface::class);
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->localeCodeAwareContextBuilder = new LocaleCodeAwareContextBuilder(
            $this->decoratedContextBuilder,
            LocaleCodeAware::class,
            'localeCode',
            $this->localeContext,
        );
    }

    public function testSetsLocaleCodeAsAConstructorArgument(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]]);

        $this->localeContext->expects(self::once())->method('getLocaleCode')->willReturn('en_US');

        self::assertSame([
            'input' => ['class' => SendContactRequest::class],
            AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                SendContactRequest::class => ['localeCode' => 'en_US'],
            ],
        ], $this->localeCodeAwareContextBuilder->createFromRequest($requestMock, true, []));
    }

    public function testDoesNothingIfThereIsNoInputClass(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn([]);

        $this->localeContext->expects(self::never())->method('getLocaleCode');

        self::assertSame([], $this->localeCodeAwareContextBuilder->createFromRequest($requestMock, true, []));
    }

    public function testDoesNothingIfInputClassIsNoChannelAware(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]]);

        $this->localeContext->expects(self::never())->method('getLocaleCode');

        self::assertSame(
            ['input' => ['class' => \stdClass::class]],
            $this->localeCodeAwareContextBuilder->createFromRequest($requestMock, true, []),
        );
    }
}
