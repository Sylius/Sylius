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

namespace Sylius\Bundle\GridBundle\Form\Registry;

interface FormTypeRegistryInterface
{
    public function add(string $identifier, string $typeIdentifier, string $formType): void;

    public function get(string $identifier, string $typeIdentifier): ?string;

    public function has(string $identifier, string $typeIdentifier): bool;
}
