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
     * @var LocaleNameConverterInterface
     */
    private $localeNameConverter;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $localeFactory
     * @param LocaleNameConverterInterface $localeNameConverter
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $localeFactory,
        LocaleNameConverterInterface $localeNameConverter,
        RepositoryInterface $localeRepository
    ) {
        $this->localeFactory = $localeFactory;
        $this->localeRepository = $localeRepository;
        $this->localeNameConverter = $localeNameConverter;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given the store has locale :localeName
     */
    public function theStoreHasLocale($localeName)
    {
        $locale = $this->localeFactory->createNew();
        $locale->setCode($this->localeNameConverter->convertToCode($localeName));

        $this->sharedStorage->set('locale', $locale);
        $this->localeRepository->add($locale);
    }

    /**
     * @Given the store has disabled locale :localeName
     */
    public function theStoreHasDisabledLocale($localeName)
    {
        $locale = $this->localeFactory->createNew();
        $locale->setCode($this->localeNameConverter->convertToCode($localeName));
        $locale->disable();

        $this->sharedStorage->set('locale', $locale);
        $this->localeRepository->add($locale);
    }
}
