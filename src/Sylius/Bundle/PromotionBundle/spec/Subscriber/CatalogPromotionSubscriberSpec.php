<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\PromotionBundle\Subscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionSubscriberSpec extends ObjectBehavior
{
    function let(MessageBusInterface $messageBus): void
    {
        $this->beConstructedWith($messageBus);
    }

    function it_is_a_event_subscriber(): void
    {
        $this->shouldHaveType(EventSubscriberInterface::class);
    }
}
