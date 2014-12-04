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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
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
        ;

        $formFactory = $builder->getFormFactory();

        $credentialsMustBeSent = false;
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use (&$credentialsMustBeSent, $formFactory) {
            $data = $event->getData();
            $form = $event->getForm();

            if (false == isset($data['gateway'])) {
                return;
            }



            $credentialsMustBeSent = true;
            $paypalPaymentBuilder = new PaymentBuilder();

            $credentialsBuilder = $formFactory->createBuilder('form');
            foreach ($paypalPaymentBuilder->get('payum.options') as $name => $value) {
                $credentialsBuilder->add($name, is_bool($value) ? 'checkbox' : 'text', array(
                    'constraints' => array_filter(array(
                        $paypalPaymentBuilder->get('payum.required_options', $name) ? new NotBlank : null
                    )),
                    'data' => $value,
                    'required' => (bool)$paypalPaymentBuilder->get('payum.required_options', $name),
                ));
            }

            $form->add('credentials', $credentialsBuilder->getForm());
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($builder, &$credentialsMustBeSent) {
            $form = $event->getForm();

            if ($form->has('credentials') && $credentialsMustBeSent) {
                $form->get('credentials')->addError(new FormError('Credentials must be set'));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_payment_method';
    }
}
