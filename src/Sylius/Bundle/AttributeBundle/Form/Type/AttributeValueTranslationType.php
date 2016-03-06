<?php

namespace Sylius\Bundle\AttributeBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTranslationType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $subjectName;

    /**
     * @param string $dataClass
     * @param array $validationGroups
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
        $valueTranslationType = $options['valueTranslationType'];
        $builder
            ->add('value', $valueTranslationType, [
                'label' => 'sylius.form.attribute.name'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefined('valueType');
        $resolver->setDefaults([
            'valueTranslationType' => 'text',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_attribute_value_translation', $this->subjectName);
    }
}
