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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Locale;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Exception\LocaleIsUsedException;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Locale\RemoveProcessor;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class RemoveProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $removeProcessorMock;

    private LocaleUsageCheckerInterface&MockObject $localeUsageCheckerMock;

    private RemoveProcessor $removeProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->removeProcessorMock = $this->createMock(ProcessorInterface::class);
        $this->localeUsageCheckerMock = $this->createMock(LocaleUsageCheckerInterface::class);
        $this->removeProcessor = new RemoveProcessor($this->removeProcessorMock, $this->localeUsageCheckerMock);
    }

    public function testThrowsAnExceptionIfObjectIsNotALocale(): void
    {
        $this->removeProcessorMock->expects(self::never())->method('process')->with($this->any());

        $this->localeUsageCheckerMock->expects(self::never())->method('isUsed');

        self::expectException(\InvalidArgumentException::class);

        $this->removeProcessor->process(new \stdClass(), new Delete());
    }

    public function testThrowsAnExceptionIfOperationIsNotDelete(): void
    {
        /** @var LocaleInterface|MockObject $localeMock */
        $localeMock = $this->createMock(LocaleInterface::class);

        $this->removeProcessorMock->expects(self::never())->method('process')->with($this->any());

        $this->localeUsageCheckerMock->expects(self::never())->method('isUsed');

        self::expectException(\InvalidArgumentException::class);

        $this->removeProcessor->process($localeMock, new Post());
    }

    public function testThrowsAnExceptionIfAGivenLocaleIsUsed(): void
    {
        /** @var LocaleInterface|MockObject $localeMock */
        $localeMock = $this->createMock(LocaleInterface::class);

        $localeMock->expects(self::atLeastOnce())->method('getCode')->willReturn('pl_PL');

        $this->localeUsageCheckerMock->expects(self::once())->method('isUsed')->with('pl_PL')->willReturn(true);

        $this->removeProcessorMock->expects(self::never())->method('process')->with($this->any());

        self::expectException(LocaleIsUsedException::class);

        $this->removeProcessor->process($localeMock, new Delete());
    }

    public function testRemovesALocale(): void
    {
        /** @var LocaleInterface|MockObject $localeMock */
        $localeMock = $this->createMock(LocaleInterface::class);

        $operation = new Delete();

        $localeMock->expects(self::once())->method('getCode')->willReturn('pl_PL');

        $this->localeUsageCheckerMock->expects(self::once())->method('isUsed')->with('pl_PL')->willReturn(false);

        $this->removeProcessorMock->expects(self::once())->method('process')->with($localeMock, $operation, [], []);

        $this->removeProcessor->process($localeMock, $operation);
    }
}
