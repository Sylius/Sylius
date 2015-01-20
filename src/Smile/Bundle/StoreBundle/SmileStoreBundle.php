<?php

namespace Smile\Bundle\StoreBundle;

use Smile\Bundle\StoreBundle\DependencyInjection\Compiler\DoctrineORMScopedMappingsPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SmileStoreBundle extends AbstractResourceBundle
{
    protected function getBundlePrefix()
    {
        return 'smile_store';
    }

    /**
     * Return array of currently supported drivers.
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
    protected function getModelInterfaces()
    {
        return array(
            'Smile\Component\Store\Context\StoreContextInterface' => 'smile.context.store.class',
            'Smile\Component\Store\Model\StoreInterface' => 'smile.model.store.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Smile\Component\Store\Model';
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $namespaces = array($this->getConfigFilesPath() => $this->getModelNamespace());
        $locator = new Definition(
            'Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator',
            array($namespaces, '.orm.xml')
        );
        $driverDefinition = new Definition('Smile\Component\Scope\Doctrine\Mapping\Driver\XmlDriver', array($locator));
        $driverDefinition->setPublic(false);
        $compilerPass = new DoctrineORMScopedMappingsPass($driverDefinition);

        $container->addCompilerPass($compilerPass);
    }
}