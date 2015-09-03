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

use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/*
 * Listener to prevent the deletion of last enabled channel.
 *
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class ChannelDeletionListener
{
    /**
     * @var ChannelRepository
     */
    private $repository;

    /**
     * @param ChannelRepository $repository
     */
    public function __construct(ChannelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Prevent channel deletion if no more channels enabled.
     *
     * @param ResourceEvent $event
     */
    public function onChannelPreDelete(ResourceEvent $event)
    {
        $resource = $event->getSubject();

        if (!$resource instanceof ChannelInterface) {
            throw new UnexpectedTypeException(
                $resource,
                'Sylius\Component\Channel\Model\ChannelInterface'
            );
        }

        $result = $this->repository->findBy(array('enabled' => true));

        if (!$result || (count($result) === 1 && current($result) === $resource)) {
            $event->stop('error.at_least_one');
        }
    }
}
