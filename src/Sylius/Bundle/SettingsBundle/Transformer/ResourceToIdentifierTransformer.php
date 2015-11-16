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

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer as BaseTransformer;

/**
 * Object to identifier transformer.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceToIdentifierTransformer extends BaseTransformer implements ParameterTransformerInterface
{
    public function transform($value)
    {
        return parent::reverseTransform($value);
    }

    public function reverseTransform($value)
    {
        return parent::transform($value);
    }
}
