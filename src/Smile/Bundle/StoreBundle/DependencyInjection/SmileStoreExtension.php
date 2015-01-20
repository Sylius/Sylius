<?php

namespace Smile\Bundle\StoreBundle\DependencyInjection;


use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class SmileStoreExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS
        );
    }
}