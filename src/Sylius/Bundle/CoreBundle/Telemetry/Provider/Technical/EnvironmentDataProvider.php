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

namespace Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical;

use Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical\EnvironmentData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class EnvironmentDataProvider implements DataProviderInterface
{
    public function __construct(private string $appEnvironment)
    {
    }

    public function provide(): TelemetryDataInterface
    {
        return new EnvironmentData(
            app: $this->appEnvironment,
            webserver: $this->getWebServerSoftware(),
            os: PHP_OS_FAMILY,
            docker: $this->isRunningInDocker(),
            ramGb: $this->getSystemMemoryInGb(),
            phpMemoryLimit: ini_get('memory_limit') ?: null,
        );
    }

    private function getWebServerSoftware(): ?string
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            return $_SERVER['SERVER_SOFTWARE'];
        }

        return PHP_SAPI;
    }

    private function isRunningInDocker(): bool
    {
        return is_file('/.dockerenv') || false !== getenv('DOCKER');
    }

    private function getSystemMemoryInGb(): ?float
    {
        return match (PHP_OS_FAMILY) {
            'Linux' => $this->getLinuxMemory(),
            'Darwin' => $this->getMacOsMemory(),
            'Windows' => $this->getWindowsMemory(),
            default => null,
        };
    }

    private function getLinuxMemory(): ?float
    {
        $memInfoPath = '/proc/meminfo';
        if (!is_readable($memInfoPath)) {
            return null;
        }

        $contents = file_get_contents($memInfoPath);
        if (false === $contents) {
            return null;
        }

        if (!preg_match('/MemTotal:\\s+(\\d+) kB/i', $contents, $matches)) {
            return null;
        }

        $kilobytes = (int) $matches[1];

        return round($kilobytes / 1024 / 1024, 2);
    }

    private function getMacOsMemory(): ?float
    {
        try {
            $output = @shell_exec('sysctl -n hw.memsize 2>/dev/null');
            if (null === $output || '' === trim($output)) {
                return null;
            }

            $bytes = (int) trim($output);
            if ($bytes <= 0) {
                return null;
            }

            return round($bytes / 1024 / 1024 / 1024, 2);
        } catch (\Throwable) {
            return null;
        }
    }

    private function getWindowsMemory(): ?float
    {
        try {
            $output = @shell_exec('wmic OS get TotalVisibleMemorySize /value 2>nul');
            if (null !== $output && preg_match('/TotalVisibleMemorySize=(\d+)/i', $output, $matches)) {
                $kilobytes = (int) $matches[1];
                if ($kilobytes > 0) {
                    return round($kilobytes / 1024 / 1024, 2);
                }
            }

            $output = @shell_exec('systeminfo | findstr /C:"Total Physical Memory" 2>nul');
            if (null !== $output && preg_match('/:\s*([0-9,\.]+)\s*MB/i', $output, $matches)) {
                $megabytes = (float) str_replace([',', '.'], ['', ''], $matches[1]);
                if ($megabytes > 0) {
                    return round($megabytes / 1024, 2);
                }
            }

            return null;
        } catch (\Throwable) {
            return null;
        }
    }
}
