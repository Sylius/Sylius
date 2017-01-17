<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ExchangeRateType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ratio', NumberType::class, [
                'label' => 'sylius.form.exchange_rate.ratio',
                'required' => true,
                'invalid_message' => 'sylius.exchange_rate.ratio.invalid',
                'scale' => 5,
                'rounding_mode' => $options['rounding_mode'],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var ExchangeRateInterface $exchangeRate */
            $exchangeRate = $event->getData();
            $form = $event->getForm();

            $disabled = null !== $exchangeRate->getId();

            $form
                ->add('sourceCurrency', CurrencyChoiceType::class, [
                    'label' => 'sylius.form.exchange_rate.source_currency',
                    'required' => true,
                    'empty_data' => false,
                    'disabled' => $disabled,
                ])
                ->add('targetCurrency', CurrencyChoiceType::class, [
                    'label' => 'sylius.form.exchange_rate.target_currency',
                    'required' => true,
                    'empty_data' => false,
                    'disabled' => $disabled,
                ])
            ;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('rounding_mode', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_exchange_rate';
    }
}
