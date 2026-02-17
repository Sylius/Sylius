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

namespace Sylius\Bundle\CoreBundle\Telemetry\DTO\Business;

use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class LocalesData implements TelemetryDataInterface
{
    /** @var list<string> */
    public $locales;

    /** @var list<string> */
    public $channelDefaultLocales;

    /** @var string */
    public $defaultLocale;

    /**
     * @param list<string> $locales
     * @param list<string> $channelDefaultLocales
     */
    public function __construct(array $locales, array $channelDefaultLocales, string $defaultLocale)
    {
        $this->locales = $locales;
        $this->channelDefaultLocales = $channelDefaultLocales;
        $this->defaultLocale = $defaultLocale;
    }

    public function normalize(): array
    {
        return [
            'locales' => $this->locales,
            'channel_default_locales' => $this->channelDefaultLocales,
            'default_locale' => $this->defaultLocale,
        ];
    }
}
