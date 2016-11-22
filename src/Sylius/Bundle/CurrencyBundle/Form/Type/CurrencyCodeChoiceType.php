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

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Liverbool <nukboon@gmail.com>
 */
final class CurrencyCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param RepositoryInterface $currencyRepository
     */
    public function __construct(RepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $choices = [];

                /** @var CurrencyInterface[] $currencies */
                $currencies = $this->currencyRepository->findBy(['enabled' => true]);
                foreach ($currencies as $currency) {
                    $choices[sprintf('%s - %s', $currency->getCode(), $currency->getName())] = $currency->getCode();
                }

                return $choices;
            },
            'choice_translation_domain' => false,
            'choices_as_values' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency_code_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_currency_code_choice';
    }
}
