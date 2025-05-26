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

namespace Tests\Sylius\Component\Core\Translation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Checker\CLIContextCheckerInterface;
use Sylius\Component\Core\Translation\TranslatableEntityLocaleAssigner;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class TranslatableEntityLocaleAssignerTest extends TestCase
{
    private LocaleContextInterface&MockObject $localeContext;

    private MockObject&TranslationLocaleProviderInterface $translationLocaleProvider;

    private CLIContextCheckerInterface&MockObject $commandBasedContextChecker;

    private MockObject&TranslatableInterface $translatableEntity;

    private TranslatableEntityLocaleAssigner $assigner;

    protected function setUp(): void
    {
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->translationLocaleProvider = $this->createMock(TranslationLocaleProviderInterface::class);
        $this->commandBasedContextChecker = $this->createMock(CLIContextCheckerInterface::class);
        $this->translatableEntity = $this->createMock(TranslatableInterface::class);
        $this->assigner = new TranslatableEntityLocaleAssigner(
            $this->localeContext,
            $this->translationLocaleProvider,
            $this->commandBasedContextChecker,
        );
    }

    public function testShouldImplementTranslatableEntityLocaleAssignerInterface(): void
    {
        $this->assertInstanceOf(TranslatableEntityLocaleAssigner::class, $this->assigner);
    }

    public function testShouldAssignCurrentAndDefaultLocaleToGivenTranslatableEntity(): void
    {
        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $this->translationLocaleProvider->expects($this->once())->method('getDefaultLocaleCode')->willReturn('pl_PL');
        $this->commandBasedContextChecker->expects($this->once())->method('isExecutedFromCLI')->willReturn(false);
        $this->translatableEntity->expects($this->once())->method('setCurrentLocale')->with('en_US');
        $this->translatableEntity->expects($this->once())->method('setFallbackLocale')->with('pl_PL');

        $this->assigner->assignLocale($this->translatableEntity);
    }

    public function testShouldAssignFallbackLocaleIfRunningFromCommand(): void
    {
        $this->localeContext->expects($this->never())->method('getLocaleCode');
        $this->translationLocaleProvider->expects($this->once())->method('getDefaultLocaleCode')->willReturn('pl_PL');
        $this->commandBasedContextChecker->expects($this->once())->method('isExecutedFromCLI')->willReturn(true);
        $this->translatableEntity->expects($this->once())->method('setCurrentLocale')->with('pl_PL');
        $this->translatableEntity->expects($this->once())->method('setFallbackLocale')->with('pl_PL');

        $this->assigner->assignLocale($this->translatableEntity);
    }

    public function testShouldAssignLocaleIfProcessIsNotRunningFromCli(): void
    {
        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $this->translationLocaleProvider->expects($this->once())->method('getDefaultLocaleCode')->willReturn('pl_PL');
        $this->translatableEntity->expects($this->once())->method('setCurrentLocale')->with('en_US');
        $this->translatableEntity->expects($this->once())->method('setFallbackLocale')->with('pl_PL');
        $this->commandBasedContextChecker->expects($this->once())->method('isExecutedFromCLI')->willReturn(false);

        $this->assigner->assignLocale($this->translatableEntity);
    }

    public function testShouldUseDefaultLocaleAsCurrentIfCouldNotResolveTheCurrentLocale(): void
    {
        $this->localeContext->expects($this->once())->method('getLocaleCode')->willThrowException(new LocaleNotFoundException());
        $this->translationLocaleProvider->expects($this->once())->method('getDefaultLocaleCode')->willReturn('pl_PL');
        $this->commandBasedContextChecker->expects($this->once())->method('isExecutedFromCLI')->willReturn(false);
        $this->translatableEntity->expects($this->once())->method('setCurrentLocale')->with('pl_PL');
        $this->translatableEntity->expects($this->once())->method('setFallbackLocale')->with('pl_PL');

        $this->assigner->assignLocale($this->translatableEntity);
    }
}
