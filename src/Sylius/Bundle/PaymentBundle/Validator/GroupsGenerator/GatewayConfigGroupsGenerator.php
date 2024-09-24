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

namespace Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator;

use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigGroupsGenerator implements GatewayConfigGroupsGeneratorInterface
{
    /**
     * @param array<string> $defaultValidationGroups
     * @param array<string, array<string, string>> $validationGroups
     */
    public function __construct(private array $defaultValidationGroups, private array $validationGroups)
    {
    }

    public function __invoke(GatewayConfigInterface $gatewayConfig): array
    {
        if ($gatewayConfig->getFactoryName() === null) {
            return $this->defaultValidationGroups;
        }

        return $this->validationGroups[$gatewayConfig->getFactoryName()] ?? $this->defaultValidationGroups;
    }
}
