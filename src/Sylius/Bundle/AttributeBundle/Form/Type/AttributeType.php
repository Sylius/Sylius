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
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeType extends AbstractResourceType
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
     * @param string $dataClass
     * @param array $validationGroups
     * @param string $subjectName
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
            ->add('name', 'text', array(
                'label' => 'sylius.form.attribute.name',
            ))
            ->add('code', 'text', array(
                'label' => 'sylius.form.attribute.code',
            ))
            ->add('translations', 'a2lix_translationsForms', array(
                'form_type' => sprintf('sylius_%s_attribute_translation', $this->subjectName),
                'label' => 'sylius.form.attribute.presentation',
            ))
            ->add('type', 'sylius_attribute_type_choice', array(
                'label' => 'sylius.form.attribute.type',
            ))
            ->addEventSubscriber(new BuildAttributeFormChoicesListener($builder->getFormFactory(), $this->attributeTypeRegistry))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_attribute', $this->subjectName);
    }
}
