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

namespace spec\Sylius\Bundle\AddressingBundle\Form\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AddressingBundle\Form\EventListener\BuildZoneFormSubscriber;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class BuildZoneFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(BuildZoneFormSubscriber::class);
    }

    function it_is_a_subscriber(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event(): void
    {
        $this::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ]);
    }

    function it_fixes_members_keys_on_pre_submit(
        FormEvent $event,
        FormInterface $form,
        ZoneInterface $zone,
        ZoneMemberInterface $belgiumZone,
        ZoneMemberInterface $germanyZone,
        ZoneMemberInterface $franceZone,
    ): void {
        $event->getData()->willReturn([
            'name' => 'Europe',
            'code' => 'EU',
            'members' => [
                0 => ['code' => 'EU-BE'],
                3 => ['code' => 'EU-PL'],
                4 => ['code' => 'EU-FR'],
            ],
        ]);

        $event->getForm()->willReturn($form);
        $form->getData()->willReturn($zone);

        $belgiumZone->getCode()->willReturn('EU-BE');
        $germanyZone->getCode()->willReturn('EU-DE');
        $franceZone->getCode()->willReturn('EU-FR');

        $zone->getMembers()->willReturn(new ArrayCollection([
            $belgiumZone->getWrappedObject(),
            $germanyZone->getWrappedObject(),
            $franceZone->getWrappedObject(),
        ]));

        $event
            ->setData([
                'name' => 'Europe',
                'code' => 'EU',
                'members' => [
                    0 => ['code' => 'EU-BE'],
                    2 => ['code' => 'EU-FR'],
                    3 => ['code' => 'EU-PL'],
                ],
            ])
            ->shouldBeCalled()
        ;

        $this->preSubmit($event);
    }

    function it_does_nothing_if_there_are_no_members_in_the_form_data(FormEvent $event): void
    {
        $event->getData()->willReturn([
            'name' => 'Europe',
            'code' => 'EU',
        ]);

        $event->setData(Argument::any())->shouldNotBeCalled();

        $this->preSubmit($event);
    }

    function it_ignores_members_with_no_code(
        FormEvent $event,
        FormInterface $form,
        ZoneInterface $zone,
        ZoneMemberInterface $belgiumZone,
        ZoneMemberInterface $germanyZone,
        ZoneMemberInterface $franceZone,
    ): void {
        $event->getData()->willReturn([
            'name' => 'Europe',
            'code' => 'EU',
            'members' => [
                0 => ['code' => 'EU-BE'],
                3 => [],
                4 => ['code' => 'EU-FR'],
            ],
        ]);

        $event->getForm()->willReturn($form);
        $form->getData()->willReturn($zone);

        $belgiumZone->getCode()->willReturn('EU-BE');
        $germanyZone->getCode()->willReturn('EU-DE');
        $franceZone->getCode()->willReturn('EU-FR');

        $zone->getMembers()->willReturn(new ArrayCollection([
            $belgiumZone->getWrappedObject(),
            $germanyZone->getWrappedObject(),
            $franceZone->getWrappedObject(),
        ]));

        $event
            ->setData([
                'name' => 'Europe',
                'code' => 'EU',
                'members' => [
                    0 => ['code' => 'EU-BE'],
                    2 => ['code' => 'EU-FR'],
                ],
            ])
            ->shouldBeCalled()
        ;

        $this->preSubmit($event);
    }

    public function it_throws_an_exception_if_from_data_is_not_a_zone(
        FormEvent $event,
        FormInterface $form,
    ): void {
        $event->getData()->willReturn([
            'name' => 'Europe',
            'code' => 'EU',
            'members' => [
                0 => ['code' => 'EU-BE'],
                3 => ['code' => 'EU-PL'],
                4 => ['code' => 'EU-FR'],
            ],
        ]);

        $event->getForm()->willReturn($form);
        $form->getData()->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('preSubmit', [$event])
        ;
    }
}
