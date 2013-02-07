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

/**
 * Promotion form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
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
            ->add('rule', 'sylius_promotion_rule', array(
                'label' => 'sylius.form.promotion.rule'
            ))
            ->add('action', 'sylius_promotion_action', array(
                'label' => 'sylius.form.promotion.action'
            ))
        ;
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
