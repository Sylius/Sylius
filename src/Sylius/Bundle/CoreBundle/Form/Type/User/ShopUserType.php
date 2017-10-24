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

namespace Sylius\Bundle\CoreBundle\Form\Type\User;

use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShopUserType extends UserType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('username')
            ->remove('email')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_shop_user';
    }
}
