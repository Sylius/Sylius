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

use Symfony\Component\Validator\Constraints\Composite;

/**
 * @internal
 */
final class TypedConfigurationCollection extends Composite
{
    public string $message = 'sylius.custom_configuration';
    public array $types = [];

    public function validatedBy(): string
    {
        return 'sylius_typed_configuration_collection';
    }

    public function getRequiredOptions(): array
    {
        return ['types'];
    }

    protected function getCompositeOption(): string
    {
        return 'types';
    }
}
