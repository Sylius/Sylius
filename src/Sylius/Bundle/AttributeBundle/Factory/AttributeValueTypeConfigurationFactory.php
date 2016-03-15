<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Factory;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration\AttributeValueTypeConfiguration;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration\AttributeValueTypeTranslationConfiguration;
use Sylius\Component\Attribute\Model\AttributeInterface;

/**
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTypeConfigurationFactory implements AttributeValueTypeConfigurationFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(AttributeInterface $attribute, $subjectName, $counter = 0)
    {
        if ($attribute->isTranslatable()) {
            return new AttributeValueTypeTranslationConfiguration($attribute, $subjectName, $counter);
        }

        return new AttributeValueTypeConfiguration($attribute, $subjectName, $counter);

    }
}
