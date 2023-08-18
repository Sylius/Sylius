<?php

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class UniqueProvinceCollectionCodes extends Constraint
{
    public string $message = 'sylius.country.unique_provinces_codes';

    public function validatedBy(): string
    {
        return 'sylius_unique_province_collection_codes_validator';
    }
}
