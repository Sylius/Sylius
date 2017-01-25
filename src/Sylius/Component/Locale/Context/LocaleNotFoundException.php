<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Context;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LocaleNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Locale could not be found!', 0, $previousException);
    }

    /**
     * @param string $localeCode
     *
     * @return self
     */
    public static function notFound($localeCode)
    {
        return new self(sprintf('Locale "%s" cannot be found!', $localeCode));
    }

    /**
     * @param string $localeCode
     * @param array $availableLocalesCodes
     *
     * @return self
     */
    public static function notAvailable($localeCode, array $availableLocalesCodes)
    {
        return new self(sprintf(
            'Locale "%s" is not available! The available ones are: "%s".',
            $localeCode,
            implode('", "', $availableLocalesCodes)
        ));
    }
}
