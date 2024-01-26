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

namespace Sylius\Bundle\LocaleBundle\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleUsageChecker implements LocaleUsageCheckerInterface
{
    /**
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     */
    public function __construct(
        private RepositoryInterface $localeRepository,
        private RegistryInterface $resourceRegistry,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws LocaleNotFoundException
     */
    public function isUsed(string $localeCode): bool
    {
        $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);

        if (null === $locale) {
            throw new LocaleNotFoundException();
        }

        $translationEntityInterfaces = $this->getTranslationEntityInterfaces();

        foreach ($translationEntityInterfaces as $entityInterface) {
            if ($this->isLocaleUsedByTranslation($entityInterface, $localeCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, class-string>
     */
    private function getTranslationEntityInterfaces(): array
    {
        $translationEntityInterfaces = [];

        foreach ($this->resourceRegistry->getAll() as $resource) {
            $resourceParameters = $resource->getParameters();

            if (isset($resourceParameters['translation']['classes']['interface'])) {
                $translationEntityInterfaces[] = $resourceParameters['translation']['classes']['interface'];
            }
        }

        return $translationEntityInterfaces;
    }

    /**
     * @param class-string<TranslationInterface> $translationEntityInterface
     */
    private function isLocaleUsedByTranslation(string $translationEntityInterface, string $localeCode): bool
    {
        /** @var EntityRepository<TranslationInterface> $repository */
        $repository = $this->entityManager->getRepository($translationEntityInterface);

        return $repository->count(['locale' => $localeCode]) > 0;
    }
}
