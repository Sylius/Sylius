<?php

namespace Smile\Bundle\StoreBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineORMScopedMappingsPass implements CompilerPassInterface
{
    /**
     * @var Definition
     */
    protected $driverDefinition;

    /**
     * @param Definition $driverDefinition
     */
    public function __construct(Definition $driverDefinition)
    {
        $this->driverDefinition = $driverDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $scopedDriverId = uniqid();
        $container->setDefinition($scopedDriverId, $this->driverDefinition);

        // Define service if not already defined
        if (!$container->hasDefinition('smile.store_aware.driver_chain')) {
            $driverChain = new Definition('Metadata\Driver\DriverChain');
            $driverChain->setPublic(false);
            $container->addDefinitions(array('smile.store_aware.driver_chain' => $driverChain));
        }

        // Add store_aware driver to driver chain
        $container->getDefinition('smile.store_aware.driver_chain')
            ->addMethodCall('addDriver', array(new Reference($scopedDriverId)));
    }
}