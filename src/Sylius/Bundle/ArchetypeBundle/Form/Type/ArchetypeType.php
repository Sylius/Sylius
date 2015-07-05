<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ArchetypeBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Product archetype form type.
 *
 * @author Adam Elsodaney <adam.elso@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ArchetypeType extends AbstractResourceType
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @param string $dataClass
     * @param array  $validationGroups
     * @param string $subject
     */
    function __construct($dataClass, array $validationGroups, $subject)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', array(
                'label' => 'sylius.form.archetype.code'
            ))
            ->add('translations', 'a2lix_translationsForms', array(
                'form_type' => sprintf('sylius_%s_archetype_translation', $this->subject),
                'label'    => 'sylius.form.archetype.name'
            ))
            ->add('parent', sprintf('sylius_%s_archetype_choice', $this->subject), array(
                'required' => false,
                'label' => 'sylius.form.archetype.parent',
                'property' => 'name'
            ))
            ->add('attributes', sprintf('sylius_%s_attribute_choice', $this->subject), array(
                'required' => false,
                'multiple' => true,
                'label'    => 'sylius.form.archetype.attributes'
            ))
            ->add('options', sprintf('sylius_%s_option_choice', $this->subject), array(
                'required' => false,
                'multiple' => true,
                'label'    => 'sylius.form.archetype.options'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_archetype', $this->subject);
    }
}
