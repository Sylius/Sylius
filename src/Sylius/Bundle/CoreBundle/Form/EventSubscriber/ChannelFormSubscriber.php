<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ChannelFormSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || empty($data['defaultLocale']) || empty($data['baseCurrency'])) {
            return;
        }

        $data['locales'] = $this->resolveLocales(
            isset($data['locales']) ? $data['locales'] : [],
            $data['defaultLocale'])
        ;

        $data['currencies'] = $this->resolveCurrencies(
            isset($data['currencies']) ? $data['currencies'] : [],
            $data['baseCurrency'])
        ;

        $event->setData($data);
    }

    /**
     * @param string[] $locales
     * @param string $defaultLocale
     *
     * @return string[]
     */
    private function resolveLocales(array $locales, $defaultLocale)
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
     * @param string[] $currencies
     * @param string $baseCurrency
     *
     * @return string[]
     */
    private function resolveCurrencies(array $currencies, $baseCurrency)
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
