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

namespace Tests\Sylius\Component\Product\Generator;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Product\Generator\SlugGenerator;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AllowMockObjectsWithoutExpectations]
final class SlugGeneratorTest extends TestCase
{
    private MockObject&SluggerInterface $slugger;

    private SlugGenerator $slugGenerator;

    protected function setUp(): void
    {
        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->slugger->method('slug')->willReturnCallback((new AsciiSlugger())->slug(...));
        $this->slugGenerator = new SlugGenerator($this->slugger);
    }

    public function testImplementsSlugGeneratorInterface(): void
    {
        self::assertInstanceOf(SlugGeneratorInterface::class, $this->slugGenerator);
    }

    public function testGeneratesSlugBasedOnGivenName(): void
    {
        $this->assertSame(
            'cyclades',
            $this->slugGenerator->generate('Cyclades'),
        );
        $this->assertSame(
            'small-world',
            $this->slugGenerator->generate('Small World'),
        );
    }

    public function testGeneratesSlugWithoutPunctuationMarks(): void
    {
        $this->assertSame(
            'ticket-to-ride-europe',
            $this->slugGenerator->generate('"Ticket to Ride: Europe"'),
        );
        $this->assertSame(
            'tzolk-in-the-mayan-calendar',
            $this->slugGenerator->generate('Tzolk\'in: The Mayan Calendar'),
        );
        $this->assertSame(
            'game-of-thrones-the-board-game',
            $this->slugGenerator->generate('Game of Thrones: The Board Game'),
        );
    }

    public function testGeneratesSlugReplacingApostrophesWithHyphens(): void
    {
        $this->assertSame(
            'rock-n-roll',
            $this->slugGenerator->generate("Rock'n'roll"),
        );
    }

    public function testGeneratesSlugWithoutSpecialSigns(): void
    {
        $this->assertSame(
            'wsiasc-do-pociagu-europa',
            $this->slugGenerator->generate('Wsiąść do Pociągu: Europa'),
        );
    }

    public function testGeneratesSlugUsingBehatTransliteratorWhenNoSluggerProvided(): void
    {
        $generator = new SlugGenerator(null);

        $this->assertSame('board-games', $generator->generate('Board games'));
        $this->assertSame('rock-n-roll', $generator->generate("Rock'n'roll"));
    }
}
