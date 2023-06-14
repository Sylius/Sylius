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

namespace Sylius\Bundle\UserBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\UserBundle\EventListener\UpdateUserEncoderListener;
use Sylius\Bundle\UserBundle\EventListener\UserDeleteListener;
use Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber;
use Sylius\Bundle\UserBundle\EventListener\UserReloaderListener;
use Sylius\Bundle\UserBundle\Factory\UserWithEncoderFactory;
use Sylius\Bundle\UserBundle\Provider\AbstractUserProvider;
use Sylius\Bundle\UserBundle\Provider\EmailProvider;
use Sylius\Bundle\UserBundle\Provider\UsernameOrEmailProvider;
use Sylius\Bundle\UserBundle\Provider\UsernameProvider;
use Sylius\Bundle\UserBundle\Reloader\UserReloader;
use Sylius\Component\User\Security\Checker\TokenUniquenessChecker;
use Sylius\Component\User\Security\Generator\UniquePinGenerator;
use Sylius\Component\User\Security\Generator\UniqueTokenGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Http\SecurityEvents;

final class SyliusUserExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        $loader->load('services.xml');

        $this->createParameters($config['resources'], $container);
        $this->createServices($config['resources'], $container);
        $this->loadEncodersAwareServices($config['encoder'], $config['resources'], $container);
    }

    private function resolveResources(array $resources, ContainerBuilder $container): array
    {
        $container->setParameter('sylius.user.users', $resources);

        $resolvedResources = [];
        foreach ($resources as $variableName => $variableConfig) {
            foreach ($variableConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$variableName . '_' . $resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
    }

    private function createServices(array $resources, ContainerBuilder $container): void
    {
        foreach ($resources as $userType => $config) {
            $userClass = $config['user']['classes']['model'];

            $this->createTokenGenerators($userType, $config['user'], $container);
            $this->createReloaders($userType, $container);
            $this->createLastLoginListeners($userType, $userClass, $config['user'], $container);
            $this->createProviders($userType, $userClass, $container);
            $this->createUserDeleteListeners($userType, $container);
        }
    }

    private function createParameters(array $resources, ContainerBuilder $container): void
    {
        foreach ($resources as $userType => $config) {
            $this->createResettingTokenParameters($userType, $config['user'], $container);
        }
    }

    private function loadEncodersAwareServices(?string $globalEncoder, array $resources, ContainerBuilder $container): void
    {
        foreach ($resources as $userType => $config) {
            $encoder = $config['user']['encoder'] ?? $globalEncoder;

            if (null === $encoder || false === $encoder) {
                continue;
            }

            $this->overwriteResourceFactoryWithEncoderAwareFactory($container, $userType, $encoder);
            $this->registerUpdateUserEncoderListener($container, $userType, $encoder, $config);
        }
    }

    private function createTokenGenerators(string $userType, array $config, ContainerBuilder $container): void
    {
        $this->createUniquenessCheckers($userType, $config, $container);

        $container->setDefinition(
            sprintf('sylius.%s_user.token_generator.password_reset', $userType),
            $this->createTokenGeneratorDefinition(
                UniqueTokenGenerator::class,
                [
                    new Reference('sylius.random_generator'),
                    new Reference(sprintf('sylius.%s_user.token_uniqueness_checker.password_reset', $userType)),
                    $config['resetting']['token']['length'],
                ],
            ),
        )->setPublic(true);

        $container->setDefinition(
            sprintf('sylius.%s_user.pin_generator.password_reset', $userType),
            $this->createTokenGeneratorDefinition(
                UniquePinGenerator::class,
                [
                    new Reference('sylius.random_generator'),
                    new Reference(sprintf('sylius.%s_user.pin_uniqueness_checker.password_reset', $userType)),
                    $config['resetting']['pin']['length'],
                ],
            ),
        )->setPublic(true);

        $container->setDefinition(
            sprintf('sylius.%s_user.token_generator.email_verification', $userType),
            $this->createTokenGeneratorDefinition(
                UniqueTokenGenerator::class,
                [
                    new Reference('sylius.random_generator'),
                    new Reference(sprintf('sylius.%s_user.token_uniqueness_checker.email_verification', $userType)),
                    $config['verification']['token']['length'],
                ],
            ),
        )->setPublic(true);
    }

    private function createTokenGeneratorDefinition(string $generatorClass, array $arguments): Definition
    {
        $generatorDefinition = new Definition($generatorClass);
        $generatorDefinition->setArguments($arguments);

        return $generatorDefinition;
    }

    private function createUniquenessCheckers(string $userType, array $config, ContainerBuilder $container): void
    {
        $repositoryServiceId = sprintf('sylius.repository.%s_user', $userType);

        $resetPasswordTokenUniquenessCheckerDefinition = new Definition(TokenUniquenessChecker::class);
        $resetPasswordTokenUniquenessCheckerDefinition->addArgument(new Reference($repositoryServiceId));
        $resetPasswordTokenUniquenessCheckerDefinition->addArgument($config['resetting']['token']['field_name']);
        $container->setDefinition(
            sprintf('sylius.%s_user.token_uniqueness_checker.password_reset', $userType),
            $resetPasswordTokenUniquenessCheckerDefinition,
        );

        $resetPasswordPinUniquenessCheckerDefinition = new Definition(TokenUniquenessChecker::class);
        $resetPasswordPinUniquenessCheckerDefinition->addArgument(new Reference($repositoryServiceId));
        $resetPasswordPinUniquenessCheckerDefinition->addArgument($config['resetting']['pin']['field_name']);
        $container->setDefinition(
            sprintf('sylius.%s_user.pin_uniqueness_checker.password_reset', $userType),
            $resetPasswordPinUniquenessCheckerDefinition,
        );

        $emailVerificationTokenUniquenessCheckerDefinition = new Definition(TokenUniquenessChecker::class);
        $emailVerificationTokenUniquenessCheckerDefinition->addArgument(new Reference($repositoryServiceId));
        $emailVerificationTokenUniquenessCheckerDefinition->addArgument($config['verification']['token']['field_name']);
        $container->setDefinition(
            sprintf('sylius.%s_user.token_uniqueness_checker.email_verification', $userType),
            $emailVerificationTokenUniquenessCheckerDefinition,
        );
    }

    private function createReloaders(string $userType, ContainerBuilder $container): void
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

    private function createLastLoginListeners(string $userType, string $userClass, array $config, ContainerBuilder $container): void
    {
        $managerServiceId = sprintf('sylius.manager.%s_user', $userType);
        $lastLoginListenerServiceId = sprintf('sylius.listener.%s_user_last_login', $userType);

        $lastLoginListenerDefinition = new Definition(UserLastLoginSubscriber::class);
        $lastLoginListenerDefinition->setArguments([
            new Reference($managerServiceId),
            $userClass,
            $config['login_tracking_interval'] ?? null,
        ]);
        $lastLoginListenerDefinition->addTag('kernel.event_subscriber');
        $container->setDefinition($lastLoginListenerServiceId, $lastLoginListenerDefinition);
    }

    public function createUserDeleteListeners(string $userType, ContainerBuilder $container): void
    {
        $userDeleteListenerServiceId = sprintf('sylius.listener.%s_user_delete', $userType);
        $userPreDeleteEventName = sprintf('sylius.%s_user.pre_delete', $userType);

        $userDeleteListenerDefinition = new Definition(UserDeleteListener::class);
        $userDeleteListenerDefinition->addArgument(new Reference('security.token_storage'));
        $userDeleteListenerDefinition->addArgument(new Reference('request_stack'));
        $userDeleteListenerDefinition->addTag('kernel.event_listener', ['event' => $userPreDeleteEventName, 'method' => 'deleteUser']);
        $container->setDefinition($userDeleteListenerServiceId, $userDeleteListenerDefinition);
    }

    private function createProviders(string $userType, string $userModel, ContainerBuilder $container): void
    {
        $repositoryServiceId = sprintf('sylius.repository.%s_user', $userType);
        $abstractProviderServiceId = sprintf('sylius.%s_user_provider', $userType);
        $providerEmailBasedServiceId = sprintf('sylius.%s_user_provider.email_based', $userType);
        $providerNameBasedServiceId = sprintf('sylius.%s_user_provider.name_based', $userType);
        $providerEmailOrNameBasedServiceId = sprintf('sylius.%s_user_provider.email_or_name_based', $userType);

        $abstractProviderDefinition = new Definition(AbstractUserProvider::class);
        $abstractProviderDefinition->setAbstract(true);
        $abstractProviderDefinition->setLazy(true);
        $abstractProviderDefinition->addArgument($userModel);
        $abstractProviderDefinition->addArgument(new Reference($repositoryServiceId));
        $abstractProviderDefinition->addArgument(new Reference('sylius.canonicalizer'));
        $container->setDefinition($abstractProviderServiceId, $abstractProviderDefinition);

        $emailBasedProviderDefinition = new ChildDefinition($abstractProviderServiceId);
        $emailBasedProviderDefinition->setClass(EmailProvider::class);
        $container->setDefinition($providerEmailBasedServiceId, $emailBasedProviderDefinition);

        $nameBasedProviderDefinition = new ChildDefinition($abstractProviderServiceId);
        $nameBasedProviderDefinition->setClass(UsernameProvider::class);
        $container->setDefinition($providerNameBasedServiceId, $nameBasedProviderDefinition);

        $emailOrNameBasedProviderDefinition = new ChildDefinition($abstractProviderServiceId);
        $emailOrNameBasedProviderDefinition->setClass(UsernameOrEmailProvider::class);
        $container->setDefinition($providerEmailOrNameBasedServiceId, $emailOrNameBasedProviderDefinition);
    }

    private function overwriteResourceFactoryWithEncoderAwareFactory(ContainerBuilder $container, string $userType, string $encoder): void
    {
        $factoryServiceId = sprintf('sylius.factory.%s_user', $userType);

        $factoryDefinition = new Definition(
            UserWithEncoderFactory::class,
            [
                $container->getDefinition($factoryServiceId),
                $encoder,
            ],
        );
        $factoryDefinition->setPublic(true);

        $container->setDefinition($factoryServiceId, $factoryDefinition);
    }

    private function registerUpdateUserEncoderListener(ContainerBuilder $container, string $userType, string $encoder, array $resourceConfig): void
    {
        $updateUserEncoderListenerDefinition = new Definition(UpdateUserEncoderListener::class, [
            new Reference(sprintf('sylius.manager.%s_user', $userType)),
            $encoder,
            $resourceConfig['user']['classes']['model'],
            $resourceConfig['user']['classes']['interface'],
            '_password',
        ]);
        $updateUserEncoderListenerDefinition->addTag('kernel.event_listener', ['event' => SecurityEvents::INTERACTIVE_LOGIN]);

        $container->setDefinition(
            sprintf('sylius.%s_user.listener.update_user_encoder', $userType),
            $updateUserEncoderListenerDefinition,
        );
    }

    private function createResettingTokenParameters(string $userType, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('sylius.%s_user.token.password_reset.ttl', $userType), $config['resetting']['token']['ttl']);
    }
}
