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

final class FormTypeRegistry implements FormTypeRegistryInterface
{
    /**
     * @var array
     */
    private $formTypes = [];

    /**
     * {@inheritdoc}
     */
    public function add(string $identifier, string $typeIdentifier, string $formType): void
    {
        $this->formTypes[$identifier][$typeIdentifier] = $formType;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $identifier, string $typeIdentifier): ?string
    {
        if (!$this->has($identifier, $typeIdentifier)) {
            return null;
        }

        return $this->formTypes[$identifier][$typeIdentifier];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier, string $typeIdentifier): bool
    {
        return isset($this->formTypes[$identifier][$typeIdentifier]);
    }
}
