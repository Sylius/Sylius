<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\MoneyBundle\Form\Type;

use Sylius\Bundle\MoneyBundle\Form\DataTransformer\SyliusMoneyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MoneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
     * @psalm-suppress MissingPropertyType
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['currency'] = $options['currency'];
    }

    public function getParent(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\MoneyType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'divisor' => 100,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_money';
    }
}
