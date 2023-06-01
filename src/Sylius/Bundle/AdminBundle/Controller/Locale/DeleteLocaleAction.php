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

namespace Sylius\Bundle\AdminBundle\Controller\Locale;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatableMessage;

final class DeleteLocaleAction
{
    public function __construct (
        private RepositoryInterface $localeRepository,
        private LocaleUsageCheckerInterface $localeUsageChecker,
        private ObjectManager $localeManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var LocaleInterface|null $locale */
        $locale = $this->localeRepository->find($request->attributes->get('id'));

        if ($locale === null) {
            throw new LocaleNotFoundException(
                sprintf('Locale with id %s has not been found.', $request->attributes->get('id'))
            );
        }

        /** @var Session $session */
        $session = $request->getSession();

        if ($this->localeUsageChecker->isUsed($locale->getCode())) {
            $session->getFlashBag()->add(
                'error',
                new TranslatableMessage(
                    'sylius.locale.delete.is_used',
                    ['%locale%' => $locale->getName()],
                    'flashes',
                ),
            );

            return new RedirectResponse($request->headers->get('referer'));
        }

        $this->localeManager->remove($locale);
        $this->localeManager->flush();

        $session->getFlashBag()->add(
            'success',
            new TranslatableMessage(
                'sylius.locale.delete.success',
                ['%locale%' => $locale->getName()],
                'flashes',
            ),
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
