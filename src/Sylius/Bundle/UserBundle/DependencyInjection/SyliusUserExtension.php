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
use Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber;
use Sylius\Bundle\UserBundle\EventListener\UserReloaderListener;
use Sylius\Bundle\UserBundle\Provider\AbstractUserProvider;
use Sylius\Bundle\UserBundle\Provider\EmailProvider;
use Sylius\Bundle\UserBundle\Provider\UsernameOrEmailProvider;
use Sylius\Bundle\UserBundle\Provider\UsernameProvider;
use Sylius\Bundle\UserBundle\Reloader\UserReloader;
use Sylius\Component\User\Security\Checker\TokenUniquenessChecker;
use Sylius\Component\User\Security\Generator\UniquePinGenerator;
use Sylius\Component\User\Security\Generator\UniqueTokenGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
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

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $this->createServices($config['resources'], $container);
    }

    /**
     * Resolve resources for every subject.
     *
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
     * @param ContainerBuilder $container
     */
    private function createServices(array $resources, ContainerBuilder $container)
    {
        foreach ($resources as $userType => $config) {
            $this->createTokenGenerators($userType, $config['user'], $container);
            $this->createReloaders($userType, $container);
            $this->createLastLoginListeners($userType, $container);
            $this->createProviders($userType, $container);
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

        $passwordResetTokenGeneratorDefinition = new Definition(UniqueTokenGenerator::class);
        $passwordResetTokenGeneratorDefinition->addArgument(
            new Reference(sprintf('sylius.%s_user.checker.token_uniqueness.password_reset', $userType))
        );
        $passwordResetTokenGeneratorDefinition->addArgument($config['resetting']['token']['length']);
        $container->setDefinition(
            sprintf('sylius.%s_user.generator.password_reset_token', $userType),
            $passwordResetTokenGeneratorDefinition
        );

        $passwordResetPinGeneratorDefinition = new Definition(UniquePinGenerator::class);
        $passwordResetPinGeneratorDefinition->addArgument(
            new Reference(sprintf('sylius.%s_user.checker.pin_uniqueness.password_reset', $userType))
        );
        $passwordResetPinGeneratorDefinition->addArgument($config['resetting']['pin']['length']);
        $container->setDefinition(
            sprintf('sylius.%s_user.generator.password_reset_pin', $userType),
            $passwordResetPinGeneratorDefinition
        );

        $emailVerificationTokenGeneratorDefinition = new Definition(UniqueTokenGenerator::class);
        $emailVerificationTokenGeneratorDefinition->addArgument(
            new Reference(sprintf('sylius.%s_user.checker.token_uniqueness.email_verification', $userType))
        );
        $emailVerificationTokenGeneratorDefinition->addArgument($config['verification']['token']['length']);
        $container->setDefinition(
            sprintf('sylius.%s_user.generator.email_verification_token', $userType),
            $emailVerificationTokenGeneratorDefinition
        );
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
        $userReloaderListenerDefinition->addTag('kernel.event_listener', ['event' => 'prePersist', 'method' => 'reloadUser']);
        $userReloaderListenerDefinition->addTag('kernel.event_listener', ['event' => 'preUpdate', 'method' => 'reloadUser']);
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
    private function createProviders($userType, ContainerBuilder $container)
    {
        $repositoryServiceId = sprintf('sylius.repository.%s_user', $userType);
        $abstractProviderServiceId = sprintf('sylius.%s_user.provider', $userType);
        $providerEmailBasedServiceId = sprintf('sylius.%s_user.provider.email_based', $userType);
        $providerNameBasedServiceId = sprintf('sylius.%s_user.provider.name', $userType);
        $providerEmailOrNameBasedServiceId = sprintf('sylius.%s_user.provider.email_or_name_based', $userType);

        $abstractProviderDefinition = new Definition(AbstractUserProvider::class);
        $abstractProviderDefinition->setAbstract(true);
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
}
