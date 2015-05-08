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
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Shipping rule form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleType extends AbstractResourceType
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    public function __construct($dataClass, array $validationGroups, ServiceRegistryInterface $registry)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'sylius_shipping_rule_choice', array(
                'label' => 'sylius.form.rule.type'
            ))
            ->addEventSubscriber(new BuildRuleFormListener($this->registry, $builder->getFormFactory()))
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
