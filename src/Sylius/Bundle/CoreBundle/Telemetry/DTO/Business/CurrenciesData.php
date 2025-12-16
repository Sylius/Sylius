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
final class CurrenciesData implements TelemetryDataInterface
{
    /** @param list<string> $currencies */
    public function __construct(
        public array $currencies,
    ) {
    }

    public function normalize(): array
    {
        return [
            'currencies' => $this->currencies,
        ];
    }
}
