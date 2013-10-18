<?php

namespace Sylius\Bundle\ReviewBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;

class SyliusReviewBundle extends Bundle
{
	/**
     * Return array of currently supported database drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Sylius\Bundle\ReviewBundle\Model\ReviewInterface' => 'sylius.model.review.class',
            'Sylius\Bundle\ReviewBundle\Model\GuestReviewerInterface' => 'sylius.model.guest_reviewer.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_review', $interfaces));

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Sylius\Bundle\ReviewBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_review.driver.doctrine/orm'));
    }
}
