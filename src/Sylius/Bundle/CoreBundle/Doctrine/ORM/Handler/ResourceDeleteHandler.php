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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Sylius\Bundle\ResourceBundle\Controller\ResourceDeleteHandlerInterface;
use Sylius\Component\Resource\Exception\DeleteHandlingException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ResourceDeleteHandler implements ResourceDeleteHandlerInterface
{
    public function __construct(private ResourceDeleteHandlerInterface $decoratedHandler, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws DeleteHandlingException
     */
    public function handle(ResourceInterface $resource, RepositoryInterface $repository): void
    {
        $this->entityManager->beginTransaction();

        try {
            $this->decoratedHandler->handle($resource, $repository);

            $this->entityManager->commit();
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->entityManager->rollback();

            throw new DeleteHandlingException(
                'Cannot delete, the resource is in use.',
                'delete_error',
                409,
                0,
                $exception,
            );
        } catch (ORMException $exception) {
            $this->entityManager->rollback();

            throw new DeleteHandlingException(
                'Ups, something went wrong during deleting a resource, please try again.',
                'something_went_wrong_error',
                500,
                0,
                $exception,
            );
        }
    }
}
