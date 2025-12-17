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
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LocaleContextBuilder;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;

final class LocaleContextBuilderTest extends TestCase
{
    private MockObject&SerializerContextBuilderInterface $decoratedSerializerContextBuilder;

    private LocaleContextInterface&MockObject $localeContext;

    private LocaleContextBuilder $localeContextBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedSerializerContextBuilder = $this->createMock(SerializerContextBuilderInterface::class);
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->localeContextBuilder = new LocaleContextBuilder($this->decoratedSerializerContextBuilder, $this->localeContext);
    }

    public function testUpdatesAnContextWhenLocaleContextHasLocale(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->decoratedSerializerContextBuilder->expects(self::once())
            ->method('createFromRequest')
            ->with($requestMock, true, []);

        $this->localeContext->expects(self::once())->method('getLocaleCode')->willReturn('en_US');

        $this->localeContextBuilder->createFromRequest($requestMock, true, []);
    }
}
