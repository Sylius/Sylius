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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Component\Resource\Exception\RaceConditionException;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ResourceUpdateHandler implements ResourceUpdateHandlerInterface
{
    public function __construct(private ResourceUpdateHandlerInterface $decoratedHandler, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws RaceConditionException
     */
    public function handle(
        ResourceInterface $resource,
        RequestConfiguration $requestConfiguration,
        ObjectManager $manager,
    ): void {
        $this->entityManager->beginTransaction();

        try {
            $this->decoratedHandler->handle($resource, $requestConfiguration, $manager);

            $this->entityManager->commit();
        } catch (OptimisticLockException $exception) {
            $this->entityManager->rollback();

            throw new RaceConditionException($exception);
        }
    }
}
