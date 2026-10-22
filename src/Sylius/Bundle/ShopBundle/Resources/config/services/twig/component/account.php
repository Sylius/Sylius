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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\ShopBundle\Form\Type\AddressType;
use Sylius\Bundle\ShopBundle\Form\Type\CustomerDefaultAddressType;
use Sylius\Bundle\ShopBundle\Form\Type\CustomerProfileType;
use Sylius\Bundle\ShopBundle\Form\Type\CustomerRegistrationType;
use Sylius\Bundle\ShopBundle\Form\Type\UserChangePasswordType;
use Sylius\Bundle\ShopBundle\Twig\Component\Account\Address\DefaultFormComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Account\Address\FormComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Account\ChangePasswordFormComponent;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.twig.component.account.register.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.customer'),
            service('form.factory'),
            '%sylius.model.customer.class%',
            CustomerRegistrationType::class,
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:account:register:form'])
    ;

    $services
        ->set('sylius_shop.twig.component.account.profile_update.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.customer'),
            service('form.factory'),
            '%sylius.model.customer.class%',
            CustomerProfileType::class,
        ])
        ->tag('sylius.live_component.shop_account', ['key' => 'sylius_shop:account:profile_update:form'])
    ;

    $services
        ->set('sylius_shop.twig.component.account.change_password_form', ChangePasswordFormComponent::class)
        ->args([
            service('form.factory'),
            UserChangePasswordType::class,
        ])
        ->tag('sylius.live_component.shop_account', ['key' => 'sylius_shop:account:change_password_form'])
    ;

    $services
        ->set('sylius_shop.twig.component.account.address.default_form', DefaultFormComponent::class)
        ->args([
            service('sylius.repository.customer'),
            service('form.factory'),
            '%sylius.model.customer.class%',
            CustomerDefaultAddressType::class,
        ])
        ->tag('sylius.live_component.shop_account', ['key' => 'sylius_shop:account:address:default_form'])
    ;

    $services
        ->set('sylius_shop.twig.component.account.address.form', FormComponent::class)
        ->args([
            service('sylius.repository.address'),
            service('form.factory'),
            '%sylius.model.address.class%',
            AddressType::class,
        ])
        ->tag('sylius.live_component.shop_account', ['key' => 'sylius_shop:account:address:form'])
    ;
};
