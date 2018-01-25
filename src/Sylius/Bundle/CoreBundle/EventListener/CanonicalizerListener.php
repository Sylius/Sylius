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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface;

final class CanonicalizerListener
{
    /**
     * @var CanonicalizerInterface
     */
    private $canonicalizer;

    /**
     * @param CanonicalizerInterface $canonicalizer
     */
    public function __construct(CanonicalizerInterface $canonicalizer)
    {
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function canonicalize(LifecycleEventArgs $event): void
    {
        $item = $event->getEntity();

        if ($item instanceof CustomerInterface) {
            $item->setEmailCanonical($this->canonicalizer->canonicalize($item->getEmail()));
        } elseif ($item instanceof UserInterface) {
            $item->setUsernameCanonical($this->canonicalizer->canonicalize($item->getUsername()));
            $item->setEmailCanonical($this->canonicalizer->canonicalize($item->getEmail()));
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->canonicalize($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->canonicalize($event);
    }
}
