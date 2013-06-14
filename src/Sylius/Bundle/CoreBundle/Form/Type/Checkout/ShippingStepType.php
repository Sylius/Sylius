<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Checkout shipping step form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingStepType extends AbstractType
{
    protected $dataClass;
    protected $translator;

    public function __construct($dataClass, TranslatorInterface $translator)
    {
        $this->dataClass = $dataClass;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = new NotBlank();
        $notBlank->message = $this->translator->trans('sylius.checkout.shipping_method.not_blank');

        $builder
            ->add('shippingMethod', 'sylius_shipping_method_choice', array(
                'label'       => 'sylius.form.checkout.shipping_method',
                'shippables'  => $options['shippables'],
                'criteria'    => $options['criteria'],
                'expanded'    => true,
                'constraints' => array(
                    $notBlank
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
            ->setRequired(array(
                'shippables'
            ))
            ->setOptional(array(
                'criteria'
            ))
            ->setAllowedTypes(array(
                'shippables' => 'Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface',
                'criteria'   => array('array')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_checkout_shipping';
    }
}
