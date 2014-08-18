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

use Symfony\Component\PropertyAccess\PropertyAccess;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectToIdentifierTransformer as BaseTransformer;

/**
 * Object to identifier transformer.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ObjectToIdentifierTransformer extends BaseTransformer implements ParameterTransformerInterface
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
