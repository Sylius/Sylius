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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class LocaleContext implements Context
{
    /**
     * @var FactoryInterface
     */
    private $localeFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param FactoryInterface $localeFactory
     * @param RepositoryInterface $localeRepository
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        FactoryInterface $localeFactory,
        RepositoryInterface $localeRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->localeFactory = $localeFactory;
        $this->localeRepository = $localeRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^the store has locale "([^"]*)"$/
     */
    public function theStoreHasLocale($localeName)
    {
        $locale = $this->localeFactory->createNew();
        $locale->setCode($this->convertToCode($localeName));

        $this->sharedStorage->set('locale', $locale);
        $this->localeRepository->add($locale);
    }

    /**
     * @Given /^the store has disabled locale "([^"]*)"$/
     */
    public function theStoreHasDisabledLocale($localeName)
    {
        $locale = $this->localeFactory->createNew();
        $locale->setCode($this->convertToCode($localeName));
        $locale->disable();

        $this->sharedStorage->set('locale', $locale);
        $this->localeRepository->add($locale);
    }

    /**
     * @param string $localeName
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function convertToCode($localeName)
    {
        $localeNames = Intl::getLocaleBundle()->getLocaleNames('en');
        $localeCode = array_search($localeName, $localeNames, true);

        if (false === $localeCode) {
            throw new \InvalidArgumentException(
                sprintf('Cannot find code for %s locale', $localeName)
            );
        }

        return $localeCode;
    }
}
