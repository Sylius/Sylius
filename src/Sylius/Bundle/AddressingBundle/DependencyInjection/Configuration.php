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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Configuration extends AbstractResourceConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_addressing');

        $this
            ->addDefaults($rootNode, null, null, array(
                'address'              => array('sylius'),
                'country'              => array('sylius'),
                'province'             => array('sylius'),
                'zone'                 => array('sylius'),
                'zone_member'          => array('sylius'),
                'zone_member_country'  => array('sylius'),
                'zone_member_province' => array('sylius'),
                'zone_member_zone'     => array('sylius'),
            ))
            ->addScopesSection($rootNode)
        ;
        $rootNode
            ->append($this->createResourcesSection(array(
                'address'              => array(
                    'model' => 'Sylius\Component\Addressing\Model\Address',
                    'form'  => array(
                        self::DEFAULT_KEY => 'Sylius\Bundle\AddressingBundle\Form\Type\AddressType',
                        'choice'          => null,
                    ),
                ),
                'country'              => array(
                    'model'               => 'Sylius\Component\Addressing\Model\Country',
                    'repository'          => 'Sylius\Bundle\ResourceBundle\Doctrine\ORM\TranslatableEntityRepository',
                    'form'                => array(
                        self::DEFAULT_KEY => 'Sylius\Bundle\AddressingBundle\Form\Type\CountryType'
                    ),
                    'translatable_fields' => array('name'),
                ),
                'country_translation'  => array(
                    'model' => 'Sylius\Component\Addressing\Model\CountryTranslation',
                ),
                'province'             => array(
                    'model'      => 'Sylius\Component\Addressing\Model\Province',
                    'controller' => 'Sylius\Bundle\AddressingBundle\Controller\ProvinceController',
                    'form'       => array(
                        self::DEFAULT_KEY => 'Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType',
                        'choice'          => 'Sylius\Bundle\AddressingBundle\Form\Type\ProvinceChoiceType',
                    ),
                ),
                'zone'                 => array(
                    'model' => 'Sylius\Component\Addressing\Model\Zone',
                    'form'  => array(
                        self::DEFAULT_KEY => 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneType',
                    ),
                ),
                'zone_member'          => array(
                    'model' => 'Sylius\Component\Addressing\Model\ZoneMember',
                    'form'  => array(
                        self::DEFAULT_KEY => 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType',
                        'choice'          => null,
                    ),
                ),
                'zone_member_country'  => array(
                    'model' => 'Sylius\Component\Addressing\Model\ZoneMemberCountry',
                    'form'  => array(
                        self::DEFAULT_KEY => 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberCountryType',
                        'choice'          => null,
                    ),
                ),
                'zone_member_province' => array(
                    'model' => 'Sylius\Component\Addressing\Model\ZoneMemberProvince',
                    'form'  => array(
                        self::DEFAULT_KEY => 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberProvinceType',
                    ),
                ),
                'zone_member_zone'     => array(
                    'model' => 'Sylius\Component\Addressing\Model\ZoneMemberZone',
                    'form'  => array(
                        self::DEFAULT_KEY => 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberZoneType',
                    ),
                ),
            )))
        ;

        return $treeBuilder;
    }

    private function addScopesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('scopes')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }
}
