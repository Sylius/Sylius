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

namespace Sylius\Component\Core\Telemetry\Generator;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

/** @internal */
final class InstallationIdGenerator implements InstallationIdGeneratorInterface
{
    public function __construct(
        private string $salt,
    ) {
    }

    public function generate(Request $request): string
    {
        if ('' === trim($this->salt)) {
            return '';
        }

        $hostname = $this->getHostname($request);
        if ('' === $hostname) {
            return '';
        }

        $saltNamespace = Uuid::uuid5(Uuid::NAMESPACE_DNS, $this->salt);

        return Uuid::uuid5($saltNamespace, $hostname)->toString();
    }

    private function getHostname(Request $request): string
    {
        $host = $request->getHost();
        if (null !== $host && '' !== trim($host)) {
            return mb_strtolower(trim($host));
        }

        $systemHostname = gethostname();
        if (false !== $systemHostname && '' !== trim($systemHostname)) {
            return mb_strtolower(trim($systemHostname));
        }

        return '';
    }
}
