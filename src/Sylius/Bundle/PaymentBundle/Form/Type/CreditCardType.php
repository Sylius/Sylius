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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Dylan Johnson <eponymi.dev@gmail.com>
 */
final class CreditCardType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.form.credit_card.type',
                'expanded' => true,
              ])
              ->add('cardholderName', TextType::class, [
                'label' => 'sylius.form.credit_card.cardholder_name',
              ])
              ->add('number', TextType::class, [
                  'label' => 'sylius.form.credit_card.number',
              ])
              ->add('securityCode', TextType::class, [
                  'label' => 'sylius.form.credit_card.security_code',
              ])
              ->add('expiryMonth', ChoiceType::class, [
                  'choices' => $this->getMonthChoices(),
                  'choice_translation_domain' => false,
                  'label' => 'sylius.form.credit_card.expiry_month',
              ])
              ->add('expiryYear', ChoiceType::class, [
                  'choices' => $this->getViableYears(),
                  'choice_translation_domain' => false,
                  'label' => 'sylius.form.credit_card.expiry_year',
              ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_credit_card';
    }

    /**
     * Get years to add as choices in expiryYear.
     *
     * @return array
     */
    private function getViableYears()
    {
        $yearChoices = [];
        $currentYear = (int) date('Y');

        for ($i = 0; $i <= 20; ++$i) {
            $yearChoices[$currentYear + $i] = $currentYear + $i;
        }

        return $yearChoices;
    }

    /**
     * @return array
     */
    private function getMonthChoices()
    {
        $monthChoices = [];

        foreach (range(1, 12) as $month) {
            $monthChoices[str_pad($month, 2, 0, STR_PAD_LEFT)] = $month;
        }

        return $monthChoices;
    }
}
