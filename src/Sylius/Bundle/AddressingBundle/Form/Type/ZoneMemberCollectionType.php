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

use Sylius\Bundle\AddressingBundle\Form\EventListener\ResizeZoneMemberCollectionListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Zone member collection form type.
 *
 * @author Tim Nagel <t.nagel@infinite.net.au>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMemberCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $prototypes = $this->buildPrototypes($builder, $options);

        if ($options['allow_add'] && $options['prototype']) {
            $builder->setAttribute('prototypes', $prototypes);
        }

        $resizeListener = new ResizeZoneMemberCollectionListener(
            $builder->getFormFactory(),
            $prototypes,
            $options['options'],
            $options['allow_add'],
            $options['allow_delete']
        );

        $builder->addEventSubscriber($resizeListener);
    }

    /**
     * Builds prototypes for each of the form types used for the collection.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     *
     * @return array
     */
    protected function buildPrototypes(FormBuilderInterface $builder, array $options)
    {
        $prototypes = array();
        $types = array(
            'sylius_zone_member_country',
            'sylius_zone_member_province',
            'sylius_zone_member_zone',
        );
        foreach ($types as $type) {
            $prototype = $this->buildPrototype($builder, $options['prototype_name'], $type, $options['options'])->getForm();
            $prototypes[$type] = $prototype;
        }

        return $prototypes;
    }

    /**
     * Builds an individual prototype.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param string                                       $name
     * @param string|FormTypeInterface                     $type
     * @param array                                        $options
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    protected function buildPrototype(FormBuilderInterface $builder, $name, $type, array $options)
    {
        return $builder->create($name, $type, array_replace(array(
            'label' => $name . 'label__',
        ), $options));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['allow_add']    = $options['allow_add'];
        $view->vars['allow_delete'] = $options['allow_delete'];

        if ($form->getConfig()->hasAttribute('prototypes')) {
            $view->vars['prototypes'] = array_map(function (FormInterface $prototype) use ($view) {
                return $prototype->createView($view);
            }, $form->getConfig()->getAttribute('prototypes'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'allow_add'      => true,
            'allow_delete'   => true,
            'by_reference'   => false,
            'prototype'      => true,
            'prototype_name' => '__name__',
            'type_name'      => '_type',
            'options'        => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_zone_member_collection';
    }
}
