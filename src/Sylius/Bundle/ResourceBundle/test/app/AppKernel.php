<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new \winzou\Bundle\StateMachineBundle\winzouStateMachineBundle(),
            new Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle(),
            new Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle(),
            new AppBundle\AppBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    /**
     * Hotfix for https://github.com/symfony/symfony/issues/27494
     *
     * @internal
     */
    public function process(ContainerBuilder $container): void
    {
        if ($this->environment !== 'test') {
            return;
        }

        $clientDefinition = $container->findDefinition('test.client');

        if (count($clientDefinition->getArguments()) >= 5) {
            $clientDefinition->replaceArgument(4, null);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerBaseClass()
    {
        if ('test' === $this->environment) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }
}
