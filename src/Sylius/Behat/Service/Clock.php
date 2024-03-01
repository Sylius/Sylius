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

namespace Sylius\Behat\Service;

use Symfony\Component\Clock\ClockInterface;

final class Clock implements ClockInterface
{
    public function __construct(private string $dateFilePath)
    {
    }

    public function sleep(float|int $seconds): void
    {
        // Intentionally left blank.
    }

    public function now(): \DateTimeImmutable
    {
        if (file_exists($this->dateFilePath)) {
            $dateTime = file_get_contents($this->dateFilePath);

            return new \DateTimeImmutable($dateTime);
        }

        return new \DateTimeImmutable();
    }

    public function withTimeZone(\DateTimeZone|string $timezone): static
    {
        $clone = clone $this;
        $clone->timezone = \is_string($timezone) ? new \DateTimeZone($timezone) : $timezone;

        return $clone;
    }
}
