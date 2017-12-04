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

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ChannelFormSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if (empty($data) || empty($data['defaultLocale']) || empty($data['baseCurrency'])) {
            return;
        }

        $data['locales'] = $this->resolveLocales(
            $data['locales'] ?? [],
            $data['defaultLocale'])
        ;

        $data['currencies'] = $this->resolveCurrencies(
            $data['currencies'] ?? [],
            $data['baseCurrency'])
        ;

        $event->setData($data);
    }

    /**
     * @param array|string[] $locales
     * @param string $defaultLocale
     *
     * @return array|string[]
     */
    private function resolveLocales(array $locales, string $defaultLocale): array
    {
        if (empty($locales)) {
            return [$defaultLocale];
        }

        if (!in_array($defaultLocale, $locales)) {
            $locales[] = $defaultLocale;
        }

        return $locales;
    }

    /**
     * @param array|string[] $currencies
     * @param string $baseCurrency
     *
     * @return array|string[]
     */
    private function resolveCurrencies(array $currencies, string $baseCurrency): array
    {
        if (empty($currencies)) {
            return [$baseCurrency];
        }

        if (!in_array($baseCurrency, $currencies)) {
            $currencies[] = $baseCurrency;
        }

        return $currencies;
    }
}
