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

namespace Sylius\Component\Core\Dashboard;

final class Interval
{
    private string $interval;

    private function __construct(string $interval)
    {
        $this->interval = $interval;
    }

    public function asString(): string
    {
        return $this->interval;
    }

    public static function year(): self
    {
        return new static('year');
    }

    public static function month(): self
    {
        return new static('month');
    }

    public static function week(): self
    {
        return new static('week');
    }

    public static function day(): self
    {
        return new static('day');
    }
}
