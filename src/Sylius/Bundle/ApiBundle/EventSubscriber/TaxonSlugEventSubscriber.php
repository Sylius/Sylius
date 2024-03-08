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

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class TaxonSlugEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private TaxonSlugGeneratorInterface $taxonSlugGenerator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['generateSlug', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function generateSlug(ViewEvent $event): void
    {
        $taxon = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (
            !$taxon instanceof TaxonInterface ||
            !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT], true)
        ) {
            return;
        }

        /** @var TaxonTranslationInterface $translation */
        foreach ($taxon->getTranslations() as $translation) {
            if ($translation->getSlug() !== null && $translation->getSlug() !== '') {
                continue;
            }

            if ($translation->getName() === null || $translation->getName() === '') {
                continue;
            }

            $translation->setSlug($this->taxonSlugGenerator->generate($taxon, $translation->getLocale()));
        }

        $event->setControllerResult($taxon);
    }
}
