<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
final class ZoneTypeChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => [
                    'sylius.form.zone.types.country' => ZoneInterface::TYPE_COUNTRY,
                    'sylius.form.zone.types.province' => ZoneInterface::TYPE_PROVINCE,
                    'sylius.form.zone.types.zone' => ZoneInterface::TYPE_ZONE,
                ],
                'label' => 'sylius.form.zone.type',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_zone_type_choice';
    }
}
