<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddButtonType extends AbstractType
{
    public const OPTION_TYPES = 'types';

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars[self::OPTION_TYPES] = $options[self::OPTION_TYPES];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault(self::OPTION_TYPES, [])
            ->setAllowedTypes(self::OPTION_TYPES, 'array')
        ;
    }

    public function getParent(): string
    {
        return ButtonType::class;
    }
}
