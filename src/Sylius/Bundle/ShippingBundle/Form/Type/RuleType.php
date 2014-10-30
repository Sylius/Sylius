<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ShippingBundle\Form\EventListener\BuildRuleFormListener;
use Sylius\Component\Shipping\Checker\Registry\RuleCheckerRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Shipping rule form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleType extends AbstractResourceType
{
    protected $checkerRegistry;

    public function __construct($dataClass, array $validationGroups, RuleCheckerRegistryInterface $checkerRegistry)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->checkerRegistry = $checkerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildRuleFormListener($this->checkerRegistry, $builder->getFormFactory()))
            ->add('type', 'sylius_shipping_rule_choice', array(
                'label' => 'sylius.form.rule.type'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipping_rule';
    }
}
