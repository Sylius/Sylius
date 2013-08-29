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
    protected $validationGroups;
    protected $actionRegistry;

    public function __construct($dataClass, array $validationGroups, PromotionActionRegistryInterface $actionRegistry)
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
        $this->actionRegistry = $actionRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildActionFormListener($this->actionRegistry, $builder->getFormFactory()))
            ->add('type', 'sylius_promotion_action_choice', array(
                'label' => 'sylius.form.action.type'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_promotion_action';
    }
}
