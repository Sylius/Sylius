<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Tests;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryType;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Taxation\Model\TaxCategory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function setUp()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    /**
     * @test
     */
    public function it_registers_tax_category_services()
    {
        $this->assertEquals(TaxCategory::class, $this->container->getParameter('sylius.model.tax_category.class'));

        $factoryService = $this->container->get('sylius.factory.tax_category');
        $this->assertInstanceOf(Factory::class, $factoryService);

        $repositoryService = $this->container->get('sylius.repository.tax_category');
        $this->assertInstanceOf(EntityRepository::class, $repositoryService);

        $controllerService = $this->container->get('sylius.controller.tax_category');
        $this->assertInstanceOf(ResourceController::class, $controllerService);

        $formType = $this->container->get('sylius.form.type.tax_category');
        $this->assertInstanceOf(TaxCategoryType::class, $formType);

        $choiceFormType = $this->container->get('sylius.form.type.tax_category_choice');
        $this->assertInstanceOf(ResourceChoiceType::class, $choiceFormType);
    }
}
