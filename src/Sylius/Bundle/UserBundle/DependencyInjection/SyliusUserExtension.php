<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\UserBundle\EventListener\UserDeleteListener;
use Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber;
use Sylius\Bundle\UserBundle\EventListener\UserReloaderListener;
use Sylius\Bundle\UserBundle\Form\EventSubscriber\AddUserFormSubscriber;
use Sylius\Bundle\UserBundle\Provider\AbstractUserProvider;
use Sylius\Bundle\UserBundle\Provider\EmailProvider;
use Sylius\Bundle\UserBundle\Provider\UsernameOrEmailProvider;
use Sylius\Bundle\UserBundle\Provider\UsernameProvider;
use Sylius\Bundle\UserBundle\Reloader\UserReloader;
use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\User\Security\Checker\TokenUniquenessChecker;
use Sylius\Component\User\Security\Generator\UniquePinGenerator;
use Sylius\Component\User\Security\Generator\UniqueTokenGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class SyliusUserExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        $loader->load('services.xml');

        $this->createServices($config['resources'], $config['driver'], $container);
    }

    /**
     * @param array $resources
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function resolveResources(array $resources, ContainerBuilder $container)
    {
        $container->setParameter('sylius.user.users', $resources);

        $resolvedResources = [];
        foreach ($resources as $variableName => $variableConfig) {
            foreach ($variableConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$variableName.'_'.$resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
    }

    /**
     * @param array $resources
     * @param string $driver
     * @param ContainerBuilder $container
     */
    private function createServices(array $resources, $driver, ContainerBuilder $container)
    {
        foreach ($resources as $userType => $config) {
            $this->createTokenGenerators($userType, $config['user'], $container);
            $this->createReloaders($userType, $container);
            $this->createLastLoginListeners($userType, $container);
            $this->createProviders($userType, $config['user']['classes']['model'], $container);
            $this->createFormTypes($userType, $config['user'], $container);
            $this->createAddUserTypeFromSubscribers($userType, $container);
            $this->createUserDeleteListeners($userType, $container);
        }
    }

    /**
     * @param string $userType
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function createTokenGenerators($userType, array $config, ContainerBuilder $container)
    {
        $this->createUniquenessCheckers($userType, $config, $container);

        $container->setDefinition(
            sprintf('sylius.%s_user.generator.password_reset_token', $userType),
            $this->createTokenGeneratorDefinition(
                UniqueTokenGenerator::class,
                [
                    new Reference(sprintf('sylius.%s_user.checker.token_uniqueness.password_reset', $userType)),
                    $config['resetting']['token']['length']
                ]
            )
        );

        $container->setDefinition(
            sprintf('sylius.%s_user.generator.password_reset_pin', $userType),
            $this->createTokenGeneratorDefinition(
                UniquePinGenerator::class,
                [
                    new Reference(sprintf('sylius.%s_user.checker.pin_uniqueness.password_reset', $userType)),
                    $config['resetting']['pin']['length']
                ]
            )
        );

        $container->setDefinition(
            sprintf('sylius.%s_user.generator.email_verification_token', $userType),
            $this->createTokenGeneratorDefinition(
                UniqueTokenGenerator::class,
                [
                    new Reference(sprintf('sylius.%s_user.checker.token_uniqueness.email_verification', $userType)),
                    $config['verification']['token']['length']
                ]
            )
        );
    }

    /**
     * @param string $generatorClass
     * @param array $arguments
     *
     * @return Definition
     */
    private function createTokenGeneratorDefinition($generatorClass, array $arguments)
    {
        $generatorDefinition = new Definition($generatorClass);
        $generatorDefinition->setArguments($arguments);

        return $generatorDefinition;
    }

    /**
     * @param string $userType
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function createUniquenessCheckers($userType, array $config, ContainerBuilder $container)
    {
        $repositoryServiceId = sprintf('sylius.repository.%s_user', $userType);

        $tokenUniquePasswordResetDefinition = new Definition(TokenUniquenessChecker::class);
        $tokenUniquePasswordResetDefinition->addArgument(new Reference($repositoryServiceId));
        $tokenUniquePasswordResetDefinition->addArgument($config['resetting']['token']['field_name']);
        $container->setDefinition(
            sprintf('sylius.%s_user.checker.token_uniqueness.password_reset', $userType),
            $tokenUniquePasswordResetDefinition
        );

        $pinUniquePasswordResetDefinition = new Definition(TokenUniquenessChecker::class);
        $pinUniquePasswordResetDefinition->addArgument(new Reference($repositoryServiceId));
        $pinUniquePasswordResetDefinition->addArgument($config['resetting']['pin']['field_name']);
        $container->setDefinition(
            sprintf('sylius.%s_user.checker.pin_uniqueness.password_reset', $userType),
            $pinUniquePasswordResetDefinition
        );

        $tokenUniqueEmailVerificationDefinition = new Definition(TokenUniquenessChecker::class);
        $tokenUniqueEmailVerificationDefinition->addArgument(new Reference($repositoryServiceId));
        $tokenUniqueEmailVerificationDefinition->addArgument($config['verification']['token']['field_name']);
        $container->setDefinition(
            sprintf('sylius.%s_user.checker.token_uniqueness.email_verification', $userType),
            $tokenUniqueEmailVerificationDefinition
        );
    }

    /**
     * @param string $userType
     * @param ContainerBuilder $container
     */
    private function createReloaders($userType, ContainerBuilder $container)
    {
        $managerServiceId = sprintf('sylius.manager.%s_user', $userType);
        $reloaderServiceId = sprintf('sylius.%s_user.reloader', $userType);
        $reloaderListenerServiceId = sprintf('sylius.listener.%s_user.reloader', $userType);

        $userReloaderDefinition = new Definition(UserReloader::class);
        $userReloaderDefinition->addArgument(new Reference($managerServiceId));
        $container->setDefinition($reloaderServiceId, $userReloaderDefinition);

        $userReloaderListenerDefinition = new Definition(UserReloaderListener::class);
        $userReloaderListenerDefinition->addArgument(new Reference($reloaderServiceId));
        $userReloaderListenerDefinition->addTag('kernel.event_listener', ['event' => sprintf('sylius.%s_user.post_create', $userType), 'method' => 'reloadUser']);
        $userReloaderListenerDefinition->addTag('kernel.event_listener', ['event' => sprintf('sylius.%s_user.post_update', $userType), 'method' => 'reloadUser']);
        $container->setDefinition($reloaderListenerServiceId, $userReloaderListenerDefinition);
    }

    /**
     * @param string $userType
     * @param ContainerBuilder $container
     */
    private function createLastLoginListeners($userType, ContainerBuilder $container)
    {
        $managerServiceId = sprintf('sylius.manager.%s_user', $userType);
        $lastLoginListenerServiceId = sprintf('sylius.listener.%s_user_last_login', $userType);

        $lastLoginListenerDefinition = new Definition(UserLastLoginSubscriber::class);
        $lastLoginListenerDefinition->addArgument(new Reference($managerServiceId));
        $lastLoginListenerDefinition->addTag('kernel.event_subscriber');
        $container->setDefinition($lastLoginListenerServiceId, $lastLoginListenerDefinition);
    }

    /**
     * @param string $userType
     * @param ContainerBuilder $container
     */
    public function createUserDeleteListeners($userType, ContainerBuilder $container)
    {
        $userDeleteListenerServiceId = sprintf('sylius.listener.%s_user_delete', $userType);
        $userPreDeleteEventName = sprintf('sylius.%s_user.pre_delete', $userType);

        $userDeleteListenerDefinition = new Definition(UserDeleteListener::class);
        $userDeleteListenerDefinition->addArgument(new Reference('security.token_storage'));
        $userDeleteListenerDefinition->addArgument(new Reference('session'));
        $userDeleteListenerDefinition->addTag('kernel.event_listener', ['event' => $userPreDeleteEventName, 'method' => 'deleteUser']);
        $container->setDefinition($userDeleteListenerServiceId, $userDeleteListenerDefinition);
    }

    /**
     * @param string $userType
     * @param string $userModel
     * @param ContainerBuilder $container
     */
    private function createProviders($userType, $userModel, ContainerBuilder $container)
    {
        $repositoryServiceId = sprintf('sylius.repository.%s_user', $userType);
        $abstractProviderServiceId = sprintf('sylius.%s_user.provider', $userType);
        $providerEmailBasedServiceId = sprintf('sylius.%s_user.provider.email_based', $userType);
        $providerNameBasedServiceId = sprintf('sylius.%s_user.provider.name_based', $userType);
        $providerEmailOrNameBasedServiceId = sprintf('sylius.%s_user.provider.email_or_name_based', $userType);

        $abstractProviderDefinition = new Definition(AbstractUserProvider::class);
        $abstractProviderDefinition->setAbstract(true);
        $abstractProviderDefinition->setLazy(true);
        $abstractProviderDefinition->addArgument($userModel);
        $abstractProviderDefinition->addArgument(new Reference($repositoryServiceId));
        $abstractProviderDefinition->addArgument(new Reference('sylius.user.canonicalizer'));
        $container->setDefinition($abstractProviderServiceId, $abstractProviderDefinition);

        $providerEmailBasedDefinition = new DefinitionDecorator($abstractProviderServiceId);
        $providerEmailBasedDefinition->setClass(EmailProvider::class);
        $container->setDefinition($providerEmailBasedServiceId, $providerEmailBasedDefinition);

        $providerNameBasedDefinition = new DefinitionDecorator($abstractProviderServiceId);
        $providerNameBasedDefinition->setClass(UsernameProvider::class);
        $container->setDefinition($providerNameBasedServiceId, $providerNameBasedDefinition);

        $providerEmailOrNameBasedDefinition = new DefinitionDecorator($abstractProviderServiceId);
        $providerEmailOrNameBasedDefinition->setClass(UsernameOrEmailProvider::class);
        $container->setDefinition($providerEmailOrNameBasedServiceId, $providerEmailOrNameBasedDefinition);
    }

    /**
     * @param string $userType
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function createFormTypes($userType, array $config, ContainerBuilder $container)
    {
        $formTypeId = sprintf('sylius.form.type.%s_user', $userType);
        $alias = sprintf('sylius.%s_user', $userType);
        $validationGroupsParameterName = sprintf('sylius.validation_groups.%s', $userType);
        $validationGroups = new Parameter($validationGroupsParameterName);
        $userTypeDefinition = new Definition($config['classes']['form']['default']);

        if (!$container->hasParameter($validationGroupsParameterName)) {
            $validationGroups = $config['validation_groups']['default'];
        }

        $userTypeDefinition->addArgument($config['classes']['model']);
        $userTypeDefinition->addArgument($validationGroups);
        $userTypeDefinition->addArgument($this->getMetadataDefinitionFromAlias($alias));
        $userTypeDefinition->addTag('form.type', ['alias' => sprintf('sylius_%s_user', $userType)]);
        $container->setDefinition($formTypeId, $userTypeDefinition);
    }

    /**
     * @param string $userType
     * @param ContainerBuilder $container
     */
    private function createAddUserTypeFromSubscribers($userType, ContainerBuilder $container)
    {
        $userTypeFormSubscriberId = sprintf('sylius.form.event_subscriber.add_%s_user_type', $userType);
        $userTypeFormSubscriberDefinition = new Definition(AddUserFormSubscriber::class);
        $userTypeFormSubscriberDefinition->addArgument($userType);
        $container->setDefinition($userTypeFormSubscriberId, $userTypeFormSubscriberDefinition);
    }

    /**
     * @param string $alias
     *
     * @return Definition
     */
    private function getMetadataDefinitionFromAlias($alias)
    {
        $definition = new Definition(Metadata::class);
        $definition
            ->setFactory([new Reference('sylius.resource_registry'), 'get'])
            ->setArguments([$alias])
        ;

        return $definition;
    }
}
