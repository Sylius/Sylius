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
    /**
     * @var string
     */
    private $value;

    /**
     * @var array
     */
    private static $types = [];

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return NotificationType
     */
    public static function failure(): NotificationType
    {
        return static::getTyped('failure');
    }

    /**
     * @return NotificationType
     */
    public static function success(): NotificationType
    {
        return static::getTyped('success');
    }

    /**
     * @param string $type
     *
     * @return NotificationType
     */
    private static function getTyped(string $type): NotificationType
    {
        if (!isset(static::$types[$type])) {
            static::$types[$type] = new self($type);
        }

        return static::$types[$type];
    }
}
