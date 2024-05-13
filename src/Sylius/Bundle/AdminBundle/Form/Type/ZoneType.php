<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneType as BaseZoneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class ZoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $entryOptions = $event->getForm()->get('members')->getConfig()->getOptions()['entry_options'];

            $event->getForm()->add('members', LiveCollectionType::class, [
                'entry_type' => ZoneMemberType::class,
                'entry_options' => $entryOptions,
                'button_add_options' => [
                    'label' => 'sylius.form.zone.add_member',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
            ]);
        });
    }

    public function getParent(): string
    {
        return BaseZoneType::class;
    }
}
