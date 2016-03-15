<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration;

/**
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTypeTranslationConfiguration extends AttributeValueTypeConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'translations';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'a2lix_translationsForms';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions()
    {
        return [
            'form_type' => sprintf('sylius_%s_attribute_value_translation', $this->subjectName),
            'label' => $this->getLabel(),
            'form_options' => [
                'attr' => [
                    'data-name' => 'sylius_'.$this->subjectName.'[attributes]['.$this->counter.'][translations]',
                ],
                'value_translation_type' => $this->attribute->getType(),
            ],
        ];
    }
}
