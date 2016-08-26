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
final class ImmutableLocaleContext implements LocaleContextInterface
{
    /**
     * @var string
     */
    private $localeCode;

    /**
     * @param string $localeCode
     */
    public function __construct($localeCode)
    {
        $this->localeCode = $localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }
}
