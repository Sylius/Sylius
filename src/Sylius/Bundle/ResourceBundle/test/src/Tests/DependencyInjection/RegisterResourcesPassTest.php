<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Tests\DependencyInjection;

use AppBundle\Entity\Book;
use AppBundle\Form\Type\BookType;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\ContainerHasParameterConstraint;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionHasMethodCallConstraint;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcesPass;
use Sylius\Component\Resource\Metadata\Registry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class RegisterResourcesPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_adds_method_call_to_resource_registry_if_resources_exist()
    {
        $resourceRegistry = new Definition(Registry::class);
        $this->setDefinition('sylius.resource_registry', $resourceRegistry);

        $this->setParameter(
            'sylius.resources', [
            'app.book' => [
                'classes' => [
                    'model' => Book::class,
                    'form' => [
                        'default' => BookType::class
                    ]
                ]
            ]
        ]);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.resource_registry',
            'addFromAliasAndConfiguration',
            [
                'app.book',
                [
                    'classes' => [
                        'model' => Book::class,
                        'form' => [
                            'default' => BookType::class
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function it_does_not_add_method_call_if_resources_do_not_exist()
    {
        $resourceRegistry = new Definition(Registry::class);
        $this->setDefinition('sylius.resource_registry', $resourceRegistry);

        $this->compile();

        $this->assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall(
            'sylius.resource_registry',
            'addFromAliasAndConfiguration',
            [
                'app.book',
                [
                    'classes' => [
                        'model' => Book::class,
                        'form' => [
                            'default' => BookType::class
                        ]
                    ]
                ]
            ]
        );

        $this->assertContainerBuilderNotHasParameter('sylius.resources');
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterResourcesPass());
    }

    /**
    * @param string $serviceId
    * @param string $method
    * @param array $arguments
    */
    private function assertContainerBuilderDoesNotHaveServiceDefinitionWithMethodCall($serviceId, $method, $arguments)
    {
        $definition = $this->container->findDefinition($serviceId);

        self::assertThat(
            $definition,
            new \PHPUnit_Framework_Constraint_Not(new DefinitionHasMethodCallConstraint($method, $arguments))
        );
    }

    /**
     * @param $parameterName
     * @param $expectedParameterValue
     */
    private function assertContainerBuilderNotHasParameter($parameterName, $expectedParameterValue = null)
    {
        $checkParameterValue = (func_num_args() > 1);

        self::assertThat(
            $this->container,
            new \PHPUnit_Framework_Constraint_Not(new ContainerHasParameterConstraint($parameterName, $expectedParameterValue, $checkParameterValue))
        );
    }
}
