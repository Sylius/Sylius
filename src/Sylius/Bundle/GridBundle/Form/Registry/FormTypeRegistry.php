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

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class FormTypeRegistry implements FormTypeRegistryInterface
{
    /**
     * @var array
     */
    private $formTypes = [];

    /**
     * {@inheritdoc}
     */
    public function add($identifier, $typeIdentifier, $formType)
    {
        $this->formTypes[$identifier][$typeIdentifier] = $formType;
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier, $typeIdentifier)
    {
        if (!$this->has($identifier, $typeIdentifier)) {
            return null;
        }

        return $this->formTypes[$identifier][$typeIdentifier];
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier, $typeIdentifier)
    {
        return isset($this->formTypes[$identifier][$typeIdentifier]);
    }
}
