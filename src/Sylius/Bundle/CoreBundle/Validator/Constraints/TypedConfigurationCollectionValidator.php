<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\CollectionValidator;

/**
 * @internal
 */
final class TypedConfigurationCollectionValidator extends CollectionValidator
{
    /**
     * @param array|TypedConfiguration $value
     * @param TypedConfigurationCollection $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (
            is_array($value) &&
            $this->hasTypeAndConfigurationKeys($value)
        ) {
            $type = $value['type'];
            $data = $value['configuration'];
        }
        if (
            is_object($value) &&
            $value instanceof TypedConfiguration
        ) {
            $type = $value->getType();
            $data = $value->getConfiguration();
        }
        if (!isset($type) || !isset($data)) {
            return;
        }

        parent::validate($data, $constraint->types[$type]);
    }

    private function hasTypeAndConfigurationKeys(array $data): bool
    {
        return 0 === count(array_diff(['type', 'configuration'], array_keys($data)));
    }
}
