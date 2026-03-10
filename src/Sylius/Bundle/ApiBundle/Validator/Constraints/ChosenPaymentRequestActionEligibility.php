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

/** @experimental  */
#[\Attribute]
final class ChosenPaymentRequestActionEligibility extends Constraint
{

    #[HasNamedArguments]
    public function __construct(
        string $notAvailable = 'sylius.payment_request.action_not_available',
        string $notExist = 'sylius.payment_method.not_exist',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);

        $this->notAvailable = $notAvailable;
        $this->notExist = $notExist;
    }

    public string $notAvailable = 'sylius.payment_request.action_not_available';

    public string $notExist = 'sylius.payment_method.not_exist';

    public function validatedBy(): string
    {
        return 'sylius_api_chosen_payment_request_action_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
