<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Transformer;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\EntityToIdentifierTransformer;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Object to identifier transformer.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ObjectToIdentifierTransformer extends EntityToIdentifierTransformer implements ParameterTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!is_object($value)) {
            return null;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($value, $this->identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        return $this->repository->findOneBy(array($this->identifier => $value));
    }
}
