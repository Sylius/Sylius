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

use Sylius\Bundle\MoneyBundle\Form\DataTransformer\SyliusMoneyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class MoneyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->resetViewTransformers()
            ->addViewTransformer(new SyliusMoneyTransformer(
                $options['scale'],
                $options['grouping'],
                null,
                $options['divisor']
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['currency'] = $options['currency'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return \Symfony\Component\Form\Extension\Core\Type\MoneyType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'divisor' => 100,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_money';
    }
}
