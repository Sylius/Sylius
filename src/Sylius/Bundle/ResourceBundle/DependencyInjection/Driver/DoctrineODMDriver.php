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
use Symfony\Component\DependencyInjection\Parameter;
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
    protected function getRepositoryDefinition(array $parameters)
    {
        $reflection = new \ReflectionClass($parameters['classes']['model']);
        $translatableInterface = 'Sylius\Component\Translation\Model\TranslatableInterface';
        $translatable = (interface_exists($translatableInterface) && $reflection->implementsInterface($translatableInterface));

        $repositoryClass = $translatable
            ? 'Sylius\Bundle\TranslationBundle\Doctrine\ODM\MongoDB\TranslatableResourceRepository'
            : new Parameter('sylius.mongodb_odm.repository.class');

        if (isset($parameters['classes']['repository'])) {
            $repositoryClass = $parameters['classes']['repository'];
        }

        $unitOfWorkDefinition = new Definition('Doctrine\\ODM\\MongoDB\\UnitOfWork');
        $unitOfWorkDefinition
            ->setFactory(array(new Reference($this->getManagerServiceKey()), 'getUnitOfWork'))
            ->setPublic(false);

        $definition = new Definition($repositoryClass);
        $definition->setArguments(array(
            new Reference($this->getContainerKey('manager')),
            $unitOfWorkDefinition,
            $this->getClassMetadataDefinition($parameters['classes']['model']),
        ));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceKey()
    {
        return sprintf('doctrine_mongodb.odm.%s_document_manager', $this->managerName);
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ODM\\MongoDB\\Mapping\\ClassMetadata';
    }
}
