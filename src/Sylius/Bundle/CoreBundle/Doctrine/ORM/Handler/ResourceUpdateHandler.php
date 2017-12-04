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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Component\Resource\Exception\RaceConditionException;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ResourceUpdateHandler implements ResourceUpdateHandlerInterface
{
    /**
     * @var ResourceUpdateHandlerInterface
     */
    private $decoratedHandler;

    /**
     * @param ResourceUpdateHandlerInterface $decoratedHandler
     */
    public function __construct(ResourceUpdateHandlerInterface $decoratedHandler)
    {
        $this->decoratedHandler = $decoratedHandler;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RaceConditionException
     */
    public function handle(
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ): void {
        try {
            $this->decoratedHandler->handle($resource, $configuration, $manager);
        } catch (OptimisticLockException $exception) {
            throw new RaceConditionException($exception);
        }
    }
}
