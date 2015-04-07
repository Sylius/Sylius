<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DoctrineODMDriver extends AbstractDatabaseDriver
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDriver()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepositoryDefinition(array $classes)
    {
        $reflection = new \ReflectionClass($classes['model']);
        $translatableInterface = 'Sylius\Component\Translation\Model\TranslatableInterface';
        $translatable = (interface_exists($translatableInterface) && $reflection->implementsInterface($translatableInterface));

        $repositoryClass = $translatable ? 'Sylius\Bundle\TranslationBundle\Doctrine\ODM\MongoDB\TranslatableResourceRepository' : 'Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\DocumentRepository';

        if (isset($classes['repository'])) {
            $repositoryClass = $classes['repository'];
        }

        $unitOfWorkDefinition = new Definition('Doctrine\\ODM\\MongoDB\\UnitOfWork');
        $unitOfWorkDefinition
            ->setFactoryService($this->getManagerServiceKey())
            ->setFactoryMethod('getUnitOfWork')
            ->setPublic(false);

        $definition = new Definition($repositoryClass);
        $definition->setArguments(array(
            new Reference($this->getContainerKey('manager')),
            $unitOfWorkDefinition,
            $this->getClassMetadataDefinition($classes['model']),
        ));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceKey()
    {
        return sprintf('doctrine.odm.mongodb.%_document_manager', $this->managerName);
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ODM\\MongoDB\\Mapping\\ClassMetadata';
    }
}
