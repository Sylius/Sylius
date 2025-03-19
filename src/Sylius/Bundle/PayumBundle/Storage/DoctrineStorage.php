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

use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage as BaseDoctrineStorage;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;

class DoctrineStorage extends BaseDoctrineStorage
{
    protected function doFind($id): ?object
    {
        /** @var object|GatewayConfigInterface|null $resource */
        $resource = parent::doFind($id);

        if (null === $resource) {
            return null;
        }

        if (!$resource instanceof GatewayConfigInterface) {
            return $resource;
        }

        return $resource->getUsePayum() ? $resource : null;
    }

    public function findBy(array $criteria): array
    {
        /** @var object[]|GatewayConfigInterface[] $resources */
        $resources = parent::findBy($criteria);

        return array_filter($resources, static function ($resource) {
            if (!$resource instanceof GatewayConfigInterface) {
                return true;
            }

            return $resource->getUsePayum();
        });
    }
}
