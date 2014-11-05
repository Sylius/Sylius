<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\EventListener\BuildAttributeFormChoicesListener;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Attribute\Model\AttributeTypes;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Attribute Translation type.

 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AttributeTranslationType extends AbstractResourceType
{
    /**
     * Subject name.
     *
     * @var string
     */
    protected $subjectName;

    /**
     * Constructor.
     *
     * @param string $dataClass
     * @param array  $validationGroups
     * @param string $subjectName
     */
    public function __construct($dataClass, array $validationGroups, $subjectName)
    {
       parent::__construct($dataClass, $validationGroups);

        $this->subjectName = $subjectName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('presentation', 'text', array(
                'label' => 'sylius.form.attribute.presentation'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_attribute_translation', $this->subjectName);
    }
}
