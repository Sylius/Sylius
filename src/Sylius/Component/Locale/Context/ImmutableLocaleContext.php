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

final class ImmutableLocaleContext implements LocaleContextInterface
{
    /**
     * @var string
     */
    private $localeCode;

    /**
     * @param string $localeCode
     */
    public function __construct(string $localeCode)
    {
        $this->localeCode = $localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }
}
