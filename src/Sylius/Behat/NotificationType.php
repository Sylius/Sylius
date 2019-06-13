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

namespace Sylius\Behat;

final class NotificationType
{
    /** @var string */
    private $value;

    /** @var array */
    private static $types = [];

    private function __construct(string $value)
    {
        $this->value = $value;
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
