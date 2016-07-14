<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Form\Extension;

use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MoneyTypeExtension extends AbstractTypeExtension
{
    /**
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    /**
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(CurrencyContextInterface $currencyContext)
    {
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'currency' => $this->currencyContext->getCurrency()->getCode(),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'sylius_money';
    }
}
