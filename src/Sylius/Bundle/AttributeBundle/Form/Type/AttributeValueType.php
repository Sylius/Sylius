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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Attribute value form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AttributeValueType extends AbstractResourceType
{
    /**
     * Attributes subject name.
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
            ->add('attribute', sprintf('sylius_%s_attribute_choice', $this->subjectName), array(
                'label' => sprintf('sylius.form.attribute.%s_attribute_value.attribute', $this->subjectName),
            ))
            ->addEventSubscriber(new BuildAttributeValueFormListener($builder->getFormFactory(), $this->subjectName))
        ;

        $this->buildAttributeValuePrototypes($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = array();

        foreach ($form->getConfig()->getAttribute('prototypes', array()) as $name => $prototype) {
            $view->vars['prototypes'][$name] = $prototype->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_attribute_value', $this->subjectName);
    }

    /**
     * Build attribute values' prototypes.
     *
     * @param FormBuilderInterface $builder
     */
    protected function buildAttributeValuePrototypes($builder)
    {
        $attributes = $builder->get('attribute')->getOption('choice_list')->getChoices();

        $prototypes = array();
        foreach ($attributes as $attribute) {
            $config = array_merge(array(
                'label' => sprintf('sylius.form.attribute.%s_attribute_value.value', $this->subjectName),
            ), $attribute->getConfiguration());
            $prototypes[] = $builder->create('value', $attribute->getType(), $config)->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }
}
