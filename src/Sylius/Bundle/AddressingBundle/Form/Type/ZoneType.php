<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Bundle\AddressingBundle\Model\Zone;

/**
 * Zone form type.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneType extends AbstractType
{
    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Validation groups.
     *
     * @var string
     */
    protected $validationGroups;

    /**
     * Constructor.
     *
     * @param string $dataClass
     * @param array  $validationGroups
     */
    public function __construct($dataClass, array $validationGroups)
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.zone.name'
            ))
            ->add('type', 'choice', array(
                'label'   => 'sylius.form.zone.type',
                'attr'    => array(
                    'data-form-prototype'   => 'update',
                    'data-form-prototype-prefix' => 'sylius_zone_member_',
                ),
                'choices' => Zone::getTypeChoices(),
            ))
            ->add('members', 'sylius_zone_member_collection', array(
                'label'            => false,
                'button_add_label' => 'sylius.zone.add_member',
                'allow_add'        => true,
                'allow_delete'     => true,
                'by_reference'     => false,
            ))
        ;

        $builder->setAttribute(
            'default_prototype',
            $builder->create('country', 'sylius_zone_member_country')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getConfig()->hasAttribute('default_prototype')) {
            $defaultPrototype = $form->getConfig()
                ->getAttribute('default_prototype')
                ->getForm()
                ->createView($view);

            $view->children['members']->vars['prototype'] = $defaultPrototype;
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
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_zone';
    }
}
