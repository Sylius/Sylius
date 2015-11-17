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

use Sylius\Bundle\AttributeBundle\Form\EventListener\BuildAttributeValueFormListener;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeValueType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $subjectName;

    /**
     * @var ServiceRegistryInterface
     */
    protected $attributeTypeRegistry;

    /**
     * @param string                   $dataClass
     * @param array                    $validationGroups
     * @param string                   $subjectName
     * @param ServiceRegistryInterface $attributeTypeRegistry
     */
    public function __construct($dataClass, array $validationGroups, $subjectName, ServiceRegistryInterface $attributeTypeRegistry)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->subjectName = $subjectName;
        $this->attributeTypeRegistry = $attributeTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attribute', sprintf('sylius_%s_attribute_choice', $this->subjectName), array(
                'label' => sprintf('sylius.form.attribute.%s_attribute_value.attribute', $this->subjectName),
            ))
            ->addEventSubscriber(new BuildAttributeValueFormListener($builder->getFormFactory(), $this->attributeTypeRegistry, $this->subjectName))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_attribute_value', $this->subjectName);
    }
}
