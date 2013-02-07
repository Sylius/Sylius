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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface;

/**
 * Promotion form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionType extends AbstractType
{
    protected $dataClass;
    protected $checkerRegistry;
    protected $actionRegistry;

    public function __construct($dataClass, RuleCheckerRegistryInterface $checkerRegistry, PromotionActionRegistryInterface $actionRegistry)
    {
        $this->dataClass = $dataClass;
        $this->checkerRegistry = $checkerRegistry;
        $this->actionRegistry = $actionRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.promotion.name'
            ))
            ->add('description', 'text', array(
                'label' => 'sylius.form.promotion.description'
            ))
            ->add('code', 'text', array(
                'label' => 'sylius.form.promotion.code'
            ))
            ->add('usageLimit', 'integer', array(
                'label' => 'sylius.form.promotion.usage_limit'
            ))
            ->add('startsAt', 'date', array(
                'label' => 'sylius.form.promotion.starts_at'
            ))
            ->add('endsAt', 'date', array(
                'label' => 'sylius.form.promotion.ends_at'
            ))
            ->add('rules', 'collection', array(
                'type'         => 'sylius_promotion_rule',
                'required'     => false,
                'allow_add'    => true,
                'by_reference' => false,
                'label'        => 'sylius.form.promotion.rules'
            ))
            ->add('actions', 'collection', array(
                'type'         => 'sylius_promotion_action',
                'required'     => false,
                'allow_add'    => true,
                'by_reference' => false,
                'label'        => 'sylius.form.promotion.actions'
            ))
        ;

        $prototypes = array();
        $prototypes['rules'] = array();
        foreach ($this->checkerRegistry->getCheckers() as $type => $checker) {
            $prototypes['rules'][$type] = $builder->create('__name__', $checker->getConfigurationFormType())->getForm();
        }
        $prototypes['actions'] = array();
        foreach ($this->actionRegistry->getActions() as $type => $action) {
            $prototypes['actions'][$type] = $builder->create('__name__', $action->getConfigurationFormType())->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->vars['prototypes'] = array();

        foreach ($form->getConfig()->getAttribute('prototypes') as $group => $prototypes) {
            foreach ($prototypes as $type => $prototype) {
                $view->vars['prototypes'][$group.'_'.$type] = $prototype->createView($view);
            }
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_promotion';
    }
}
