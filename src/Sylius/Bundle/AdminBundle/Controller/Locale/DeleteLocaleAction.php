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

use Sylius\Bundle\LocaleBundle\Checker\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Remover\LocaleRemoverInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DeleteLocaleAction
{
    public function __construct (private LocaleRemoverInterface $localeRemover)
    {
    }

    public function __invoke(Request $request): Response
    {
        /** @var Session $session */
        $session = $request->getSession();

        try {
            $this->localeRemover->removeById((int) $request->attributes->get('id'));

            $session->getFlashBag()->add('success', 'sylius.locale.delete.success');
        } catch (LocaleNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        } catch (LocaleIsUsedException) {
            $session->getFlashBag()->add('error', 'sylius.locale.delete.is_used');
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
