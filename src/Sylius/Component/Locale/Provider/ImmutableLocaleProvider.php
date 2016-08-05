<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Provider;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ImmutableLocaleProvider implements LocaleProviderInterface
{
    /**
     * @var array
     */
    private $availableLocalesCodes;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @var string
     */
    private $fallbackLocaleCode;

    /**
     * @param array $availableLocalesCodes
     * @param string $defaultLocaleCode
     * @param string $fallbackLocaleCode
     */
    public function __construct(array $availableLocalesCodes, $defaultLocaleCode, $fallbackLocaleCode)
    {
        $this->availableLocalesCodes = $availableLocalesCodes;
        $this->defaultLocaleCode = $defaultLocaleCode;
        $this->fallbackLocaleCode = $fallbackLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocalesCodes()
    {
        return $this->availableLocalesCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode()
    {
        return $this->defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocaleCode()
    {
        return $this->fallbackLocaleCode;
    }
}
