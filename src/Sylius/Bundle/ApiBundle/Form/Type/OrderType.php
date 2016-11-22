<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType as BaseOrderType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderType extends BaseOrderType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', ResourceChoiceType::class, [
                'resource' => 'sylius.customer',
            ])
            ->add('currencyCode', CountryCodeChoiceType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('channel', ResourceChoiceType::class, [
                'resource' => 'sylius.channel',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_api_order';
    }
}
