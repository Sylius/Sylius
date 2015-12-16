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

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ZoneType extends AbstractResourceType
{
    /**
     * @var array
     */
    protected $scopeChoices;

    /**
     * @param string   $dataClass
     * @param string[] $validationGroups
     * @param string[] $scopeChoices
     */
    public function __construct($dataClass, array $validationGroups, array $scopeChoices = array())
    {
        parent::__construct($dataClass, $validationGroups);

        $this->scopeChoices = $scopeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $zoneType = $builder->getData()->getType();

        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.zone.name',
            ))
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('type', 'sylius_zone_type_choice', array(
                'disabled' => true,
            ))
            ->add('members', 'collection', array(
                'type'             => 'sylius_zone_member',
                'button_add_label' => 'sylius.zone.add_member',
                'allow_add'        => true,
                'allow_delete'     => true,
                'by_reference'     => false,
                'delete_empty'     => true,
                'options'          => array(
                    'zone_type' => $zoneType,
                ),
            ))
        ;

        if (!empty($this->scopeChoices)) {
            $builder
                ->add('scope', 'choice', array(
                    'label'       => 'sylius.form.zone.scope',
                    'empty_value' => 'sylius.form.zone.select_scope',
                    'required'    => false,
                    'choices'     => $this->scopeChoices,
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('options', array('zone_type' => null));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_zone';
    }
}
