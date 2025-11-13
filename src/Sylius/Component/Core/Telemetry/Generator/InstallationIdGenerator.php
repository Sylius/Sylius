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
use Symfony\Component\HttpFoundation\RequestStack;

/** @internal */
final class InstallationIdGenerator implements InstallationIdGeneratorInterface
{
    public function __construct(
        private string $salt,
        private RequestStack $requestStack,
    ) {
    }

    public function generate(): string
    {
        if ('' === trim($this->salt)) {
            return '';
        }

        $hostname = $this->getHostname();
        if ('' === $hostname) {
            return '';
        }

        $saltNamespace = Uuid::uuid5(Uuid::NAMESPACE_DNS, $this->salt);

        return Uuid::uuid5($saltNamespace, $hostname)->toString();
    }

    private function getHostname(): string
    {
        $host = $this->requestStack->getMainRequest()?->getHost();
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
