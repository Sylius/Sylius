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

use Sylius\Component\Payment\Model\PaymentMethodInterface;

/** @internal */
final class PaymentMethodGroupsGenerator implements PaymentMethodGroupsGeneratorInterface
{
    /** @param array<string> $defaultValidationGroups */
    public function __construct(
        private array $defaultValidationGroups,
        private GatewayConfigGroupsGeneratorInterface $gatewayConfigGroupsGenerator,
    ) {
    }

    public function __invoke(PaymentMethodInterface $paymentMethod): array
    {
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if ($gatewayConfig === null) {
            return $this->defaultValidationGroups;
        }

        return array_unique(array_merge(
            $this->defaultValidationGroups,
            $this->gatewayConfigGroupsGenerator->__invoke($gatewayConfig),
        ));
    }
}
