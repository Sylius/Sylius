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
use Sylius\Bundle\PromotionsBundle\Form\EventListener\BuildRuleFormListener;

/**
 * Promotion rule form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleType extends AbstractType
{
    protected $dataClass;
    protected $checkerRegistry;

    public function __construct($dataClass, RuleCheckerRegistryInterface $checkerRegistry)
    {
        $this->dataClass = $dataClass;
        $this->checkerRegistry = $checkerRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildRuleFormListener($this->checkerRegistry, $builder->getFormFactory()))
            ->add('type', 'sylius_promotion_rule_choice', array(
                'label' => 'sylius.form.rule.rule'
            ))
        ;

        $prototypes = array();
        foreach ($this->checkerRegistry->getCheckers() as $type => $checker) {
            $prototypes[$type] = $builder->create('configuration', $checker->getConfigurationFormType())->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->vars['prototypes'] = array();

        foreach ($form->getConfig()->getAttribute('prototypes') as $name => $prototype) {
            $view->vars['prototypes'][$name] = $prototype->createView($view);
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
        return 'sylius_promotion_rule';
    }
}
