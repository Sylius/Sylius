<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Translation\Provider;

use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ImmutableTranslationLocaleProvider implements TranslationLocaleProviderInterface
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
        return $this->definedLocalesCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode()
    {
        return $this->defaultLocaleCode;
    }
}
