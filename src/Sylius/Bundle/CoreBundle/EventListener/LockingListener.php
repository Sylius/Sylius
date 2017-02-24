<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Model\VersionedInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class LockingListener
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param GenericEvent $event
     */
    public function lock(GenericEvent $event)
    {
        $subject = $event->getSubject();

        Assert::isInstanceOf($subject, VersionedInterface::class);

        $this->manager->lock($subject, LockMode::OPTIMISTIC, $subject->getVersion());
    }
}
