<?php

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gamil.com>
 */
class XmlFileLoaderSpec extends ObjectBehavior
{
    function let(ContainerBuilder $container, FileLocatorInterface $locator, ParameterBagInterface $parameterBag)
    {
        $this->beConstructedWith($container, $locator);

        $container->getParameterBag()->willReturn($parameterBag);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Loader\XmlFileLoader');
    }

    function it_loads_several_files($container, $locator, FileResource $fileResource)
    {
        $rootdir = __DIR__ . '/../../../../../../../ShippingBundle/Resources/config/';
        $locator->locate('services.xml')->willReturn($rootdir . 'services.xml');

        $this->loadFiles(array(
            'services',
        ));
    }

    function it_loads_drivers($container, $locator, FileResource $fileResource)
    {
        $rootdir = __DIR__ . '/../../../../../../../ShippingBundle/Resources/config/';
        $locator->locate('driver/doctrine/orm.xml')->willReturn($rootdir . 'driver/doctrine/orm.xml');

        $this->loadDriver(array(SyliusResourceBundle::DRIVER_DOCTRINE_ORM));
    }
}
