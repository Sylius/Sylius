<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ShippingMethodTypeExtension extends AbstractTypeExtension
{
    /**
     * @var ServiceRegistryInterface
     */
    private $checkerRegistry;
    /**
     * @var ServiceRegistryInterface
     */
    private $calculatorRegistry;
    /**
     * @var FormTypeRegistryInterface
     */
    private $formTypeRegistry;

    /**
     * @param ServiceRegistryInterface $checkerRegistry
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param FormTypeRegistryInterface $formTypeRegistry
     */
    public function __construct(
        ServiceRegistryInterface $checkerRegistry,
        ServiceRegistryInterface $calculatorRegistry,
        FormTypeRegistryInterface $formTypeRegistry
    ) {
        $this->checkerRegistry = $checkerRegistry;
        $this->calculatorRegistry = $calculatorRegistry;
        $this->formTypeRegistry = $formTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zone', ZoneChoiceType::class, [
                'label' => 'sylius.form.shipping_method.zone',
            ])
            ->add('taxCategory', TaxCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => '---',
                'label' => 'sylius.form.shipping_method.tax_category',
            ])
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.shipping_method.channels',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ShippingMethodType::class;
    }
}
