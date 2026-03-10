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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ChosenPaymentMethodEligibility extends Constraint
{

    #[HasNamedArguments]
    public function __construct(
        string $notAvailable = 'sylius.payment_method.not_available',
        string $notExist = 'sylius.payment_method.not_exist',
        string $paymentNotFound = 'sylius.payment.not_found',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);

        $this->notAvailable = $notAvailable;
        $this->notExist = $notExist;
        $this->paymentNotFound = $paymentNotFound;
    }

    public string $notAvailable = 'sylius.payment_method.not_available';

    public string $notExist = 'sylius.payment_method.not_exist';

    public string $paymentNotFound = 'sylius.payment.not_found';

    public function validatedBy(): string
    {
        return 'sylius_api_chosen_payment_method_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
