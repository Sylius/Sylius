<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Sylius money type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MoneyType extends AbstractType
{
    /**
     * Default currency.
     *
     * @var string
     */
    private $defaultCurrency;

    /**
     * Constructor.
     *
     * @param string $defaultCurrency
     */
    public function __construct($defaultCurrency)
    {
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'money';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'currency' => $this->defaultCurrency,
                'divisor'  => 100
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
