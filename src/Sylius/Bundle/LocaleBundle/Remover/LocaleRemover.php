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

namespace Sylius\Bundle\LocaleBundle\Remover;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\LocaleBundle\Checker\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleRemover implements LocaleRemoverInterface
{
    public function __construct (
        private RepositoryInterface $localeRepository,
        private LocaleUsageCheckerInterface $localeUsageChecker,
        private ObjectManager $localeManager,
    ) {
    }

    /** @inheritDoc */
    public function removeByCode(string $localeCode): void
    {
        $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);

        if ($locale === null) {
            throw new LocaleNotFoundException(
                sprintf('Locale with code "%s" does not exist.', $localeCode),
            );
        }

        $this->doRemove($locale);
    }

    public function removeById(int $id): void
    {
        $locale = $this->localeRepository->find($id);

        if ($locale === null) {
            throw new LocaleNotFoundException(
                sprintf('Locale with id "%s" does not exist.', $id),
            );
        }

        $this->doRemove($locale);
    }

    /**
     * @throws LocaleIsUsedException
     */
    private function doRemove(LocaleInterface $locale): void
    {
        if ($this->localeUsageChecker->isUsed($locale->getCode())) {
            throw new LocaleIsUsedException($locale->getCode());
        }

        $this->localeManager->remove($locale);
        $this->localeManager->flush();
    }
}
