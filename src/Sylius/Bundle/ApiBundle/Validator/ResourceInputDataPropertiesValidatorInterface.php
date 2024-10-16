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

use Sylius\Resource\Model\ResourceInterface;

trigger_deprecation(
    'sylius/api-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    ResourceInputDataPropertiesValidatorInterface::class,
);
/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
interface ResourceInputDataPropertiesValidatorInterface
{
    public function validate(ResourceInterface $resource, array $inputData, array $validationGroups = []): void;
}
