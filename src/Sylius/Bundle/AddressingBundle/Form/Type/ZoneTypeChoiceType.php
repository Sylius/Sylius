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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ZoneTypeChoiceType extends AbstractType
{
    /**
     * Default zone type choices.
     *
     * @var string[]
     */
    protected $choices = [
        ZoneInterface::TYPE_COUNTRY => 'sylius.form.zone.types.country',
        ZoneInterface::TYPE_PROVINCE => 'sylius.form.zone.types.province',
        ZoneInterface::TYPE_ZONE => 'sylius.form.zone.types.zone',
    ];

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => 'sylius.form.zone.type',
                'choices' => $this->choices,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_zone_type_choice';
    }
}
