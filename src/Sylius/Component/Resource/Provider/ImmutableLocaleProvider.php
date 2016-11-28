<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Provider;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ImmutableLocaleProvider implements LocaleProviderInterface
{
    /**
     * @var array
     */
    private $definedLocalesCodes;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @param array $definedLocalesCodes
     * @param string $defaultLocaleCode
     */
    public function __construct(array $definedLocalesCodes, $defaultLocaleCode)
    {
        $this->definedLocalesCodes = $definedLocalesCodes;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinedLocalesCodes()
    {
        return array_keys($this->definedLocalesCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode()
    {
        return $this->defaultLocaleCode;
    }
}
