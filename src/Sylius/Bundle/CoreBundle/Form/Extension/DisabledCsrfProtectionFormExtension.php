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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DisabledCsrfProtectionFormExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $csrfEnabled = $this->container->hasParameter('form.type_extension.csrf.enabled')
            ? $this->container->getParameter('form.type_extension.csrf.enabled')
            : true;

        if (!$csrfEnabled) {
            $resolver->setDefault('csrf_protection', false);
        }
    }
}
