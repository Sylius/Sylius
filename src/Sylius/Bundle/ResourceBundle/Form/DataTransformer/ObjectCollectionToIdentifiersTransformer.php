<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Transform object collection to array of identifiers.
 *
 * @author Liverbool <nukboon@gmail.com>
 */
class ObjectCollectionToIdentifiersTransformer extends ObjectToIdentifierTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!is_array($value) && !$value instanceof \ArrayAccess) {
            return array();
        }

        $identifiers = array();
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($value as $object) {
            $identifiers[] = $accessor->getValue($object, $this->identifier);
        }

        return $identifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return array();
        }

        return $this->repository->findBy(array($this->identifier => $value));
    }
}
