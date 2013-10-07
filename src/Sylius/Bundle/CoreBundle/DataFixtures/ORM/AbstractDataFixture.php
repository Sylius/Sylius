<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jago
 * Date: 10/6/13
 * Time: 5:10 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Hautelook\AliceBundle\Alice\DataFixtureLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractDataFixture extends DataFixtureLoader implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * Container.
     *
     * @var ContainerInterface
     */
    protected $container;


    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get service by id.
     *
     * @param string $id
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }
}