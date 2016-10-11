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

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class PromotionTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('channels', 'sylius_channel_choice', [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.promotion.channels',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PromotionType::class;
    }
}
