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
use ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface;
use Sylius\Bundle\ApiBundle\Exception\TranslationInDefaultLocaleCannotBeRemoved;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class TranslatableDataPersister implements ContextAwareDataPersisterInterface, ResumableDataPersisterInterface
{
    public function __construct(private TranslationLocaleProviderInterface $localeProvider)
    {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof TranslatableInterface;
    }

    /**
     * @param TranslatableInterface $data
     * @param array<array-key, mixed> $context
     */
    public function persist($data, array $context = []): object
    {
        $defaultLocaleCode = $this->localeProvider->getDefaultLocaleCode();

        if (!$data->getTranslations()->containsKey($defaultLocaleCode)) {
            throw new TranslationInDefaultLocaleCannotBeRemoved(
                sprintf('Translation in the default locale "%s" cannot be removed.', $defaultLocaleCode),
            );
        }

        return $data;
    }

    /**
     * @param TranslatableInterface $data
     * @param array<array-key, mixed> $context
     */
    public function remove($data, array $context = []): mixed
    {
        return $data;
    }

    /** @param array<array-key, mixed> $context */
    public function resumable(array $context = []): bool
    {
        return true;
    }
}
