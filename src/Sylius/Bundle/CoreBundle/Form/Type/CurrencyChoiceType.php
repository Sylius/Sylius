<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
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
     * @var RepositoryInterface
     */
    protected $currencyRepository;

    public function __construct(RepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = null;

        /** @var CurrencyInterface $currency */
        foreach($this->currencyRepository->findAll() as $currency) {
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
