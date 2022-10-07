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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneMemberFactoryInterface;

final class ZoneTransformer implements ZoneTransformerInterface
{
    use TransformNameToCodeAttributeTrait;

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);

        return $this->transformZoneMemberAttribute($attributes);
    }

    private function transformZoneMemberAttribute(array $attributes): array
    {
        $members = [];

        foreach ($attributes['members'] as $member) {
            if (\is_string($member)) {
                /** @var FindOrCreateResourceEvent $event */
                $event = $this->eventDispatcher->dispatch(
                    new FindOrCreateResourceEvent(ZoneMemberFactoryInterface::class, ['code' => $member])
                );

                $member = $event->getResource();
            }

            $members[] = $member;
        }

        $attributes['members'] = $members;

        return $attributes;
    }
}
