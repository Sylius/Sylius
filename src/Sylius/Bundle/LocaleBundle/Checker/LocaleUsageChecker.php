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
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Metadata\RegistryInterface;
use Sylius\Resource\Model\TranslationInterface;

final class LocaleUsageChecker implements LocaleUsageCheckerInterface
{
    /** @param RepositoryInterface<LocaleInterface> $localeRepository */
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

        foreach ($this->getTranslationEntityClasses() as $entityClass) {
            if ($this->isLocaleUsedByTranslation($entityClass, $localeCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, class-string>
     */
    private function getTranslationEntityClasses(): array
    {
        $translationEntityClasses = [];

        foreach ($this->resourceRegistry->getAll() as $resource) {
            $resourceParameters = $resource->getParameters();

            if (isset($resourceParameters['translation']['classes']['model'])) {
                $translationEntityClasses[] = $resourceParameters['translation']['classes']['model'];
            }
        }

        return $translationEntityClasses;
    }

    /**
     * @param class-string<TranslationInterface> $translationEntityClasses
     */
    private function isLocaleUsedByTranslation(string $translationEntityClasses, string $localeCode): bool
    {
        /** @var EntityRepository<TranslationInterface> $repository */
        $repository = $this->entityManager->getRepository($translationEntityClasses);

        return $repository->count(['locale' => $localeCode]) > 0;
    }
}
