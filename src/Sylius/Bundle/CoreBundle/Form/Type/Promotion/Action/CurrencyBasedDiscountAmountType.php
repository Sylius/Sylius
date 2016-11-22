<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class CurrencyBasedDiscountAmountType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var CurrencyInterface $currency
         */
        foreach ($this->currencyRepository->findAll() as $currency) {
            if ($currency->getCode() === $this->defaultCurrencyCode) {
                continue;
            }

            $builder->add($currency->getCode(), 'sylius_money', [
                'label' => $currency->getCode(),
                'constraints' => [
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency_based_discount_amount';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_currency_based_discount_amount';
    }
}
