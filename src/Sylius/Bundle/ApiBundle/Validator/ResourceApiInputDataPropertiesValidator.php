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

namespace Sylius\Bundle\ApiBundle\Validator;

use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trigger_deprecation(
    'sylius/api-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    ResourceApiInputDataPropertiesValidator::class,
);
/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class ResourceApiInputDataPropertiesValidator implements ResourceInputDataPropertiesValidatorInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(ResourceInterface $resource, array $inputData, array $validationGroups = []): void
    {
        $violations = $this->validator->startContext()->getViolations();
        foreach ($inputData as $key => $value) {
            $propertyViolations = $this->validator->validatePropertyValue(
                $resource,
                $key,
                $value,
                $validationGroups,
            );

            if ($propertyViolations->count() > 0) {
                $violations->addAll($propertyViolations);
            }
        }

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }
}
