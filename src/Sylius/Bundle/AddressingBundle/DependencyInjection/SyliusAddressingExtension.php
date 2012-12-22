<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\DependencyInjection;

use Sylius\Bundle\AddressingBundle\SyliusAddressingBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Addressing system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusAddressingExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $driver = $config['driver'];

        if (!in_array($driver, SyliusAddressingBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for SyliusAddressingBundle', $driver));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));

        $container->setParameter('sylius_addressing.driver', $driver);
        $container->setParameter('sylius_addressing.engine', $config['engine']);

        $classes = $config['classes'];

        $addressClasses = $classes['address'];

        if (isset($addressClasses['model'])) {
            $container->setParameter('sylius_addressing.model.address.class', $addressClasses['model']);
        }

        if (isset($addressClasses['repository'])) {
            $container->setParameter('sylius_addressing.repository.address.class', $addressClasses['repository']);
        }

        $container->setParameter('sylius_addressing.controller.address.class', $addressClasses['controller']);
        $container->setParameter('sylius_addressing.form.type.address.class', $addressClasses['form']);

        $countryClasses = $classes['country'];

        if (isset($countryClasses['model'])) {
            $container->setParameter('sylius_addressing.model.country.class', $countryClasses['model']);
        }

        if (isset($countryClasses['repository'])) {
            $container->setParameter('sylius_addressing.repository.country.class', $countryClasses['repository']);
        }

        $container->setParameter('sylius_addressing.controller.country.class', $countryClasses['controller']);
        $container->setParameter('sylius_addressing.form.type.country.class', $countryClasses['form']);

        $provinceClasses = $classes['province'];

        if (isset($provinceClasses['model'])) {
            $container->setParameter('sylius_addressing.model.province.class', $provinceClasses['model']);
        }

        if (isset($provinceClasses['repository'])) {
            $container->setParameter('sylius_addressing.repository.province.class', $provinceClasses['repository']);
        }

        $container->setParameter('sylius_addressing.controller.province.class', $provinceClasses['controller']);
        $container->setParameter('sylius_addressing.form.type.province.class', $provinceClasses['form']);

        $zoneClasses = $classes['zone'];

        if (isset($zoneClasses['model'])) {
            $container->setParameter('sylius_addressing.model.zone.class', $zoneClasses['model']);
        }

        if (isset($zoneClasses['repository'])) {
            $container->setParameter('sylius_addressing.repository.zone.class', $zoneClasses['repository']);
        }

        $container->setParameter('sylius_addressing.controller.zone.class', $zoneClasses['controller']);
        $container->setParameter('sylius_addressing.form.type.zone.class', $zoneClasses['form']);

        $zoneMemberClasses = $classes['zone_member'];

        if (isset($zoneMemberClasses['model'])) {
            $container->setParameter('sylius_addressing.model.zone_member.class', $zoneMemberClasses['model']);
        }

        if (isset($zoneMemberClasses['repository'])) {
            $container->setParameter('sylius_addressing.repository.zone_member.class', $zoneMemberClasses['repository']);
        }

        $container->setParameter('sylius_addressing.controller.zone_member.class', $zoneMemberClasses['controller']);
        $container->setParameter('sylius_addressing.form.type.zone_member.class', $zoneMemberClasses['form']);

        $zoneMemberCountryClasses = $classes['zone_member_country'];

        if (isset($zoneMemberCountryClasses['model'])) {
            $container->setParameter('sylius_addressing.model.zone_member_country.class', $zoneMemberCountryClasses['model']);
        }

        if (isset($zoneMemberCountryClasses['repository'])) {
            $container->setParameter('sylius_addressing.repository.zone_member_country.class', $zoneMemberCountryClasses['repository']);
        }

        $container->setParameter('sylius_addressing.form.type.zone_member_country.class', $zoneMemberCountryClasses['form']);

        $zoneMemberProvinceClasses = $classes['zone_member_province'];

        if (isset($zoneMemberCountryClasses['model'])) {
            $container->setParameter('sylius_addressing.model.zone_member_province.class', $zoneMemberProvinceClasses['model']);
        }

        if (isset($zoneMemberProvinceClasses['repository'])) {
            $container->setParameter('sylius_addressing.repository.zone_member_province.class', $zoneMemberProvinceClasses['repository']);
        }

        $container->setParameter('sylius_addressing.form.type.zone_member_province.class', $zoneMemberProvinceClasses['form']);

        $zoneMemberZoneClasses = $classes['zone_member_zone'];

        if (isset($zoneMemberZoneClasses['model'])) {
            $container->setParameter('sylius_addressing.model.zone_member_zone.class', $zoneMemberZoneClasses['model']);
        }

        if (isset($zoneMemberZoneClasses['repository'])) {
            $container->setParameter('sylius_addressing.repository.zone_member_zone.class', $zoneMemberZoneClasses['repository']);
        }

        $container->setParameter('sylius_addressing.form.type.zone_member_zone.class', $zoneMemberZoneClasses['form']);

        $loader->load('services.xml');
    }
}
