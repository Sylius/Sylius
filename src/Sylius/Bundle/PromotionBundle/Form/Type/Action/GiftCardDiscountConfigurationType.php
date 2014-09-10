<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Gift card based discount action configuration form type.
 */
class GiftCardDiscountConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function getName()
    {
        return 'sylius_promotion_action_gift_card_discount_configuration';
    }
}
