<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@gmail.com>
 */
class SyliusCurrencyBundleTest extends WebTestCase
{
    /**
     * @test
     */
    public function its_services_are_initializable()
    {
        /** @var ContainerInterface $container */
        $container = self::createClient()->getContainer();

        $services = $container->getServiceIds();

        $services = array_filter($services, function ($serviceId) {
            return 0 === strpos($serviceId, 'sylius.');
        });

        foreach ($services as $id) {
            $container->get($id);
        }
    }
}
