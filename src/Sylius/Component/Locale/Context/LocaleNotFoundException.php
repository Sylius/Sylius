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

namespace Sylius\Component\Locale\Context;

final class LocaleNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(?string $message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Locale could not be found!', 0, $previousException);
    }

    /**
     * @param string $localeCode
     *
     * @return self
     */
    public static function notFound(string $localeCode): self
    {
        return new self(sprintf('Locale "%s" cannot be found!', $localeCode));
    }

    /**
     * @param string $localeCode
     * @param array $availableLocalesCodes
     *
     * @return self
     */
    public static function notAvailable(string $localeCode, array $availableLocalesCodes): self
    {
        return new self(sprintf(
            'Locale "%s" is not available! The available ones are: "%s".',
            $localeCode,
            implode('", "', $availableLocalesCodes)
        ));
    }
}
