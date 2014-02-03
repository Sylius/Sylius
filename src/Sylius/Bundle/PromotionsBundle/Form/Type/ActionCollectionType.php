<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Form\Type;

use Sylius\Bundle\PromotionsBundle\Model\ActionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActionCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $prototypes = array();
        $types = array_keys($options['registry']->getActions());

        foreach ($types as $type) {

            $form = $builder->create($options['prototype_name'], $options['type'], array_replace(array(
                'label' => $options['prototype_name'].'label__',
                'action_type' => $type
            ), $options['options']));

            $prototypes[$type] = $form->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = array();

        foreach ($form->getConfig()->getAttribute('prototypes') as $type => $prototype) {
            $view->vars['prototypes'][$type] = $prototype->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array(
            'registry',
            'action_type'
        ));

        $resolver
            ->setDefaults(array(
                'type'             => 'sylius_promotion_action',
                'allow_add'        => true,
                'allow_delete'     => true,
                'by_reference'     => false,
                'label'            => 'sylius.form.promotion.actions',
                'button_add_label' => 'sylius.promotion.add_action',
                'action_type'      => ActionInterface::TYPE_FIXED_DISCOUNT,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_action_collection';
    }
}
