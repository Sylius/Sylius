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

namespace Sylius\Bundle\AddressingBundle\Form\EventListener;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

/** @internal */
final class BuildZoneFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if (!isset($data['members'])) {
            return;
        }

        /** @var ZoneInterface $zone */
        $zone = $event->getForm()->getData();

        Assert::isInstanceOf($zone, ZoneInterface::class);

        $membersCodes = $zone->getMembers()
            ->map(fn ($member): string => $member->getCode())
            ->getValues()
        ;

        $members = [];
        $newlyAddedMembers = [];

        foreach ($data['members'] as $member) {
            if (!isset($member['code'])) {
                continue;
            }

            $existingMemberIndex = array_search($member['code'], $membersCodes, true);

            if (false === $existingMemberIndex) {
                $newlyAddedMembers[] = $member;

                continue;
            }

            $members[$existingMemberIndex] = $member;
        }

        array_push($members, ...$newlyAddedMembers);
        $data['members'] = $members;

        $event->setData($data);
    }
}
