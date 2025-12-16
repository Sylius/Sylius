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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\LockingListener;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Resource\Model\VersionedInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class LockingListenerTest extends TestCase
{
    private EntityManagerInterface&MockObject $manager;

    private MockObject&ProductVariantResolverInterface $variantResolver;

    private LockingListener $lockingListener;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(EntityManagerInterface::class);
        $this->variantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->lockingListener = new LockingListener($this->manager);
    }

    public function testLocksVersionedEntity(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $subject = $this->createMock(VersionedInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($subject);
        $subject->expects($this->once())->method('getVersion')->willReturn(7);

        $this->manager->lock($subject, LockMode::OPTIMISTIC, 7);

        $this->lockingListener->lock($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfEventSubjectIsNotVersioned(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn('badObject');

        $this->expectException(InvalidArgumentException::class);

        $this->lockingListener->lock($event);
    }
}
