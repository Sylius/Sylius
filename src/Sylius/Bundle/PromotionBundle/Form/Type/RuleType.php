<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildRuleFormListener;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Promotion rule form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleType extends AbstractType
{
    protected $dataClass;
    protected $validationGroups;
    protected $checkerRegistry;

    public function __construct($dataClass, array $validationGroups, ServiceRegistryInterface $checkerRegistry)
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
        $this->checkerRegistry = $checkerRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildRuleFormListener($this->checkerRegistry, $builder->getFormFactory()))
            ->add('type', 'sylius_promotion_rule_choice', array(
                'label' => 'sylius.form.rule.type'
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
        return 'sylius_promotion_rule';
    }
}
