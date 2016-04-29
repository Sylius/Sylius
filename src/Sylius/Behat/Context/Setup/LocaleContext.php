<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Locale\Converter\LocaleNameConverterInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class LocaleContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $localeFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $localeFactory
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $localeFactory,
        RepositoryInterface $localeRepository
    ) {
        $this->localeFactory = $localeFactory;
        $this->localeRepository = $localeRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given the store has locale :localeCode
     * @Given the store is available in :localeCode
     */
    public function theStoreHasLocale($localeCode)
    {
        $locale = $this->localeFactory->createNew();
        $locale->setCode($localeCode);

        $this->saveLocale($locale);
    }

    /**
     * @Given the store has disabled locale :localeCode
     */
    public function theStoreHasDisabledLocale($localeCode)
    {
        $locale = $this->localeFactory->createNew();
        $locale->setCode($localeCode);
        $locale->disable();

        $this->saveLocale($locale);
    }

    /**
     * @param LocaleInterface $locale
     */
    private function saveLocale(LocaleInterface $locale)
    {
        $this->sharedStorage->set('locale', $locale);
        $this->localeRepository->add($locale);
    }
}
