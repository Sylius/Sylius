<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Validator\Constraints;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class UniqueCurrencyPairValidator extends ConstraintValidator
{
    /**
     * @var RepositoryInterface
     */
    private $exchangeRateRepository;

    /**
     * @param RepositoryInterface $exchangeRateRepository
     */
    public function __construct(RepositoryInterface $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ExchangeRateInterface) {
            throw new UnexpectedTypeException($value, ExchangeRateInterface::class);
        }

        if (null !== $value->getId()) {
            return;
        }

        if (!$this->isCurrencyPairUnique($value->getBaseCurrency(), $value->getCounterCurrency())) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    /**
     * @param CurrencyInterface $baseCurrency
     * @param CurrencyInterface $counterCurrency
     *
     * @return bool
     */
    private function isCurrencyPairUnique(CurrencyInterface $baseCurrency, CurrencyInterface $counterCurrency)
    {
        $exchangeRate = $this->exchangeRateRepository->findBy([
            'baseCurrency' => $baseCurrency,
            'counterCurrency' => $counterCurrency,
        ]);

        return [] === $exchangeRate;
    }
}
