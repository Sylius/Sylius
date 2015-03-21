<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Pricing;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CurrencyBasedConfigurationType extends AbstractType
{
    protected $currencyRepository;

    /**
     * @param RepositoryInterface $groupRepository
     */
    public function __construct(RepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->currencyRepository->findAll() as $currency) {
            $builder
                ->add($currency->getCode(), 'sylius_money', array(
                    'label'    => $currency->getCode(),
                    'required' => false,
                    'currency' => $currency->getCode()
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price_calculator_currency_based';
    }
}
