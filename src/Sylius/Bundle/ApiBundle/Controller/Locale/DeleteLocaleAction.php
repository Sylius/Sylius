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

namespace Sylius\Bundle\ApiBundle\Controller\Locale;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\LocaleBundle\Checker\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class DeleteLocaleAction
{
    public function __construct (
        private RepositoryInterface $localeRepository,
        private LocaleUsageCheckerInterface $localeUsageChecker,
        private ObjectManager $localeManager,
    ) {
    }

    /**
     * @throws LocaleNotFoundException
     * @throws LocaleIsUsedException
     */
    public function __invoke(string $code): Response
    {
        if ($this->localeUsageChecker->isUsed($code)) {
            throw new LocaleIsUsedException($code);
        }

        $locale = $this->localeRepository->findOneBy(['code' => $code]);

        if ($locale === null) {
            throw new LocaleNotFoundException($code);
        }

        $this->localeManager->remove($locale);
        $this->localeManager->flush();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
