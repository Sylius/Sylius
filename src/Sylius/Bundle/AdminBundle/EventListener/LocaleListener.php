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

namespace Sylius\Bundle\AdminBundle\EventListener;

use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

final class LocaleListener
{
    public function __construct(private LocaleUsageCheckerInterface $localeUsageChecker)
    {
    }

    public function preDelete(GenericEvent $event): void
    {
        /** @var LocaleInterface $locale */
        $locale = $event->getSubject();

        if (!$this->localeUsageChecker->isUsed($locale->getCode())) {
            return;
        }

        $event->stop('sylius.locale.delete.is_used', errorCode: Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
