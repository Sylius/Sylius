<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Provider;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Fernando Caraballo Ortiz <caraballo.ortiz@gmail.com>
 */
final class CurrencyProvider implements CurrencyProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var string
     */
    private $defaultCurrencyCode;

    /**
     * @param RepositoryInterface $currencyRepository
     * @param string $defaultCurrencyCode
     */
    public function __construct(RepositoryInterface $currencyRepository, $defaultCurrencyCode)
    {
        $this->currencyRepository = $currencyRepository;
        $this->defaultCurrencyCode = $defaultCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableCurrencies()
    {
        return $this->currencyRepository->findBy(['enabled' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrency()
    {
        return $this->currencyRepository->findOneBy(['code' => $this->defaultCurrencyCode]);
    }
}
