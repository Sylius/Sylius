<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Context;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ImmutableCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @param RepositoryInterface $currencyRepository
     * @param string $currencyCode
     */
    public function __construct(RepositoryInterface $currencyRepository, $currencyCode)
    {
        $this->currencyRepository = $currencyRepository;
        $this->currencyCode = $currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        if (!$this->initialized) {
            $this->currency = $this->currencyRepository->findOneBy(['code' => $this->currencyCode]);
            $this->initialized = true;
        }

        return $this->currency;
    }
}
