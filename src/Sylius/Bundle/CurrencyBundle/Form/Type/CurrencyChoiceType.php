<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Form\Type;

use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Sylius currency choices type.
 *
 * @author Liverbool <nukboon@gmail.com>
 */
class CurrencyChoiceType extends AbstractType
{

    /**
     * @var CurrencyProviderInterface
     */
    protected $currencyProvider;

    public function __construct(CurrencyProviderInterface $currencyProvider)
    {
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = null;

        foreach($this->currencyProvider->getAvailableCurrencies() as $currency) {
            $choices[$currency->getCode()] = sprintf('%s - %s', $currency->getCode(), $currency->getName());
        }

        $resolver->setDefaults(array(
            'choices' => $choices,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency_choice';
    }
}
