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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Bundle\ApiBundle\Exception\ZoneCannotBeRemoved;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/** @experimental */
final class ZoneDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private RepositoryInterface $zoneMemberRepository
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ZoneInterface;
    }

    public function persist($data, array $context = [])
    {
        return $this->decoratedDataPersister->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $data->getCode()]);

        if (null !== $zoneMember) {
            throw new ZoneCannotBeRemoved();
        }

        return $this->decoratedDataPersister->remove($data, $context);
    }
}
