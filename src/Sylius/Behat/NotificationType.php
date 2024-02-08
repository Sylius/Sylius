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

namespace Sylius\Behat;

final class NotificationType implements \Stringable
{
    private static array $types = [];

    private function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function failure(): self
    {
        return static::getTyped('failure');
    }

    public static function success(): self
    {
        return static::getTyped('success');
    }

    public static function info(): self
    {
        return static::getTyped('info');
    }

    private static function getTyped(string $type): self
    {
        if (!isset(static::$types[$type])) {
            static::$types[$type] = new self($type);
        }

        return static::$types[$type];
    }
}
