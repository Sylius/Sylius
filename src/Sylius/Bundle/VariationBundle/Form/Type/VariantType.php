<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Form\Type;

use Sylius\Bundle\VariationBundle\Form\EventListener\BuildVariantFormListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Variant form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantType extends AbstractType
{
    /**
     * Variable object name.
     *
     * @var string
     */
    protected $variableName;

    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Validation groups.
     *
     * @var array
     */
    protected $validationGroups;

    /**
     * Constructor.
     *
     * @param string $variableName
     * @param string $dataClass
     * @param array  $validationGroups
     */
    public function __construct($variableName, $dataClass, array $validationGroups)
    {
        $this->variableName = $variableName;
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('presentation', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.variant.presentation'
            ))
        ;

        if (!$options['master']) {
            $builder->addEventSubscriber(new BuildVariantFormListener($this->variableName, $builder->getFormFactory()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => $this->validationGroups,
                'master'            => false
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('sylius_%s_variant', $this->variableName);
    }
}
