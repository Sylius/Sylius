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
use Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface;
use Sylius\Bundle\PromotionsBundle\Form\EventListener\BuildActionFormListener;

/**
 * Promotion action form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ActionType extends AbstractType
{
    protected $dataClass;
    protected $actionRegistry;

    public function __construct($dataClass, PromotionActionRegistryInterface $actionRegistry)
    {
        $this->dataClass = $dataClass;
        $this->actionRegistry = $actionRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildActionFormListener($this->actionRegistry, $builder->getFormFactory()))
            ->add('type', 'sylius_promotion_action_choice', array(
                'label' => 'sylius.form.action.action'
            ))
        ;

        $prototypes = array();
        foreach ($this->actionRegistry->getActions() as $type => $action) {
            $prototypes[$type] = $builder->create('configuration', $action->getConfigurationFormType())->getForm();
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
        return 'sylius_promotion_action';
    }
}
