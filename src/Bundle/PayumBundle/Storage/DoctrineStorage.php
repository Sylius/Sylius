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

namespace Sylius\Bundle\PayumBundle\Storage;

use Doctrine\Persistence\ObjectManager;
use Payum\Core\Model\Identity;
use Payum\Core\Storage\AbstractStorage;

/**
 * It's a drop-in replacement for DoctrineStorage that accepts
 * Doctrine\Persistence\ObjectManager instead of Doctrine\Common\Persistence\ObjectManager.
 *
 * @internal
 *
 * @see \Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage
 */
class DoctrineStorage extends AbstractStorage
{
    public function __construct(protected ObjectManager $objectManager, $modelClass)
    {
        parent::__construct($modelClass);
    }

    public function findBy(array $criteria): array
    {
        return $this->objectManager->getRepository($this->modelClass)->findBy($criteria);
    }

    protected function doFind($id): ?object
    {
        return $this->objectManager->find($this->modelClass, $id);
    }

    protected function doUpdateModel($model): void
    {
        $this->objectManager->persist($model);
        $this->objectManager->flush();
    }

    protected function doDeleteModel($model): void
    {
        $this->objectManager->remove($model);
        $this->objectManager->flush();
    }

    protected function doGetIdentity($model): Identity
    {
        $modelMetadata = $this->objectManager->getClassMetadata($model::class);
        $id = $modelMetadata->getIdentifierValues($model);
        if (count($id) > 1) {
            throw new \LogicException('Storage not support composite primary ids');
        }

        return new Identity(array_shift($id), $model);
    }
}
