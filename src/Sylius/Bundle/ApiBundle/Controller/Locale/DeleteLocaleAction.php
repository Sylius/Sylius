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

use Sylius\Bundle\LocaleBundle\Checker\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Remover\LocaleRemoverInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class DeleteLocaleAction
{
    public function __construct(private LocaleRemoverInterface $localeRemover)
    {
    }

    /**
     * @throws LocaleNotFoundException
     * @throws LocaleIsUsedException
     */
    public function __invoke(string $code): Response
    {
        $this->localeRemover->removeByCode($code);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
