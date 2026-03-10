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

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class UniqueProvinceCollection extends Constraint
{

    #[HasNamedArguments]
    public function __construct(
        string $message = 'sylius.country.unique_provinces',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);

        $this->message = $message;
    }

    public string $message = 'sylius.country.unique_provinces';

    public function validatedBy(): string
    {
        return 'sylius_unique_province_collection_validator';
    }
}
