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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Order shipments type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShipmentType extends AbstractType
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
        $criteria = $options['criteria'];
        $channel  = $options['channel'];

        $notBlank = new NotBlank(array('groups' => array('sylius')));
        $notBlank->message = $this->translator->trans('sylius.checkout.shipping_method.not_blank');

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($notBlank, $criteria, $channel) {
                $form = $event->getForm();
                $shipment = $event->getData();

                $form->add('method', 'sylius_shipping_method_choice', array(
                    'label'       => 'sylius.form.checkout.shipping_method',
                    'subject'     => $shipment,
                    'criteria'    => $criteria,
                    'channel'     => $channel,
                    'expanded'    => true,
                    'constraints' => array(
                        $notBlank
                    )
                ));
            });
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
            ->setOptional(array(
                'criteria',
                'channel'
            ))
            ->setAllowedTypes(array(
                'criteria'   => array('array'),
                'channel'  => array('Sylius\Component\Channel\Model\ChannelInterface', 'null')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_checkout_shipment';
    }
}
