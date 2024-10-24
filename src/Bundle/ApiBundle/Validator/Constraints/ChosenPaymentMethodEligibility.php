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

use Symfony\Component\Validator\Constraint;

final class ChosenPaymentMethodEligibility extends Constraint
{
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
