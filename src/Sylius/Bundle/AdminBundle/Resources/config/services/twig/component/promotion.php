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

use Sylius\Bundle\AdminBundle\Form\Type\PromotionType;
use Sylius\Bundle\AdminBundle\Twig\Component\Promotion\FormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.twig.component.promotion.form', FormComponent::class)
        ->args([
            service('sylius.repository.promotion'),
            service('form.factory'),
            '%sylius.model.promotion.class%',
            PromotionType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:promotion:form'])
    ;
};
