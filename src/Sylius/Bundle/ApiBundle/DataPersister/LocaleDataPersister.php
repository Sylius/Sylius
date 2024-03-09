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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Bundle\ApiBundle\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class LocaleDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private LocaleUsageCheckerInterface $localeUsageChecker,
    ) {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof LocaleInterface;
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function persist($data, array $context = []): object
    {
        return $this->decoratedDataPersister->persist($data, $context);
    }

    /**
     * @param LocaleInterface $data
     * @param array<array-key, mixed> $context
     *
     * @throws LocaleIsUsedException
     */
    public function remove($data, array $context = []): mixed
    {
        if ($this->localeUsageChecker->isUsed($data->getCode())) {
            throw new LocaleIsUsedException($data->getCode());
        }

        return $this->decoratedDataPersister->remove($data, $context);
    }
}
