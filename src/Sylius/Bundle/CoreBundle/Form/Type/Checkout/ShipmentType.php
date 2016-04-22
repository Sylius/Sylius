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

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShipmentType extends AbstractType
{
    /**
     * @var string
     */
    protected $dataClass;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param string $dataClass
     * @param TranslatorInterface $translator
     */
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
        $channel = $options['channel'];

        $notBlank = new NotBlank(['groups' => ['sylius']]);
        $notBlank->message = $this->translator->trans('sylius.checkout.shipping_method.not_blank', [], 'validators');

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($notBlank, $criteria, $channel) {
                $form = $event->getForm();
                $shipment = $event->getData();

                $form->add('method', 'sylius_shipping_method_choice', [
                    'label' => 'sylius.form.checkout.shipping_method',
                    'subject' => $shipment,
                    'criteria' => $criteria,
                    'expanded' => true,
                    'constraints' => [
                        $notBlank,
                    ],
                ]);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => $this->dataClass,
            ])
            ->setDefined([
                'criteria',
                'channel',
            ])
            ->setAllowedTypes('criteria', 'array')
            ->setAllowedTypes('channel', [ChannelInterface::class, 'null'])
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
