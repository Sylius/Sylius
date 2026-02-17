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

namespace Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical;

use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class VersionData implements TelemetryDataInterface
{
    /** @var string */
    public $syliusVersion;

    /** @var string */
    public $phpVersion;

    /** @var string */
    public $symfonyVersion;

    /** @var string|null */
    public $doctrineVersion;

    /** @var string|null */
    public $twigVersion;

    /** @var string|null */
    public $apiPlatformVersion;

    public function __construct(
        string $syliusVersion,
        string $phpVersion,
        string $symfonyVersion,
        ?string $doctrineVersion,
        ?string $twigVersion,
        ?string $apiPlatformVersion
    ) {
        $this->syliusVersion = $syliusVersion;
        $this->phpVersion = $phpVersion;
        $this->symfonyVersion = $symfonyVersion;
        $this->doctrineVersion = $doctrineVersion;
        $this->twigVersion = $twigVersion;
        $this->apiPlatformVersion = $apiPlatformVersion;
    }

    public function normalize(): array
    {
        return [
            'sylius_version' => $this->syliusVersion,
            'php_version' => $this->phpVersion,
            'symfony_version' => $this->symfonyVersion,
            'doctrine_version' => $this->doctrineVersion,
            'twig_version' => $this->twigVersion,
            'api_platform_version' => $this->apiPlatformVersion,
        ];
    }
}
