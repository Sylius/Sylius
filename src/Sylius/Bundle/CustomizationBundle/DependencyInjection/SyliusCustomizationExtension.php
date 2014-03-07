<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CustomizationBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Sylius customization system container extension.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class SyliusCustomizationExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_customization')) {
            return;
        }

        $container->prependExtensionConfig('sylius_customization', array(
            'classes' => array(
                'customization' => array(
                    'model' => 'Sylius\Component\Customization\Model\Customization',
                    'form'  => 'Sylius\Bundle\CustomizationBundle\Form\Type\CustomizationType'
                ),
                'customization_value' => array(
                    'model' => 'Sylius\Component\Customization\Model\CustomizationValue',
                    'form'  => 'Sylius\Bundle\CustomizationBundle\Form\Type\CustomizationValueType'
                ),
                'customization_subject' => array(
                    'model' => 'Sylius\Component\Customization\Model\CustomizationSubject',
                    'form'  => 'Sylius\Bundle\CustomizationBundle\Form\Type\CustomizationSubjectType'
                )
            ))
        );
    }
}
