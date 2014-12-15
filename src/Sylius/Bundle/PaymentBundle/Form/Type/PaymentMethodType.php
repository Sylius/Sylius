<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Form\Type;

use Payum\Paypal\ExpressCheckout\Nvp\PaymentBuilder;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Payment\Model\PaymentMethod;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Payment method form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentMethodType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.payment_method.name'
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.form.payment_method.description'
            ))
            ->add('gateway', 'sylius_payment_gateway_choice', array(
                'label' => 'sylius.form.payment_method.gateway'
            ))
            ->add('enabled', 'checkbox', array(
                'required' => false,
                'label'    => 'sylius.form.payment_method.enabled'
            ))
            ->add('credentials', 'form');
        ;

        $that = $this;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($that) {
            /** @var  array $data */
            $data = $event->getData();

            if (false == empty($data['gateway'])) {
                $that->buildCredentials($event->getForm());
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($that) {
            /** @var  PaymentMethod $paymentMethod */
            $paymentMethod = $event->getData();

            if ($paymentMethod->getGateway()) {
                $that->buildCredentials($event->getForm(), $paymentMethod->getCredentials());
            }
        });
    }

    public function buildCredentials(Form $form)
    {
        $paypalPaymentBuilder = new PaymentBuilder();

        foreach ($paypalPaymentBuilder->get('payum.options') as $name => $value) {
            $isRequired = (bool) $paypalPaymentBuilder->get('payum.required_options', $name);
            $form->get('credentials')->add($name, is_bool($value) ? 'checkbox' : 'text', array(
                'constraints' => array_filter(array(
                    $isRequired ? new NotBlank : null
                )),
                'empty_data' => $value,
                'required' => $isRequired,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_payment_method';
    }
}
