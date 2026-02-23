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

use Sylius\Behat\Element\Product\IndexPage\VerticalMenuElement;
use Sylius\Behat\Element\Product\ShowPage\AssociationsElement;
use Sylius\Behat\Element\Product\ShowPage\AttributesElement;
use Sylius\Behat\Element\Product\ShowPage\DetailsElement;
use Sylius\Behat\Element\Product\ShowPage\LowestPriceInformationElement;
use Sylius\Behat\Element\Product\ShowPage\LowestPriceInformationElementInterface;
use Sylius\Behat\Element\Product\ShowPage\MediaElement;
use Sylius\Behat\Element\Product\ShowPage\OptionsElement;
use Sylius\Behat\Element\Product\ShowPage\PricingElement;
use Sylius\Behat\Element\Product\ShowPage\ShippingElement;
use Sylius\Behat\Element\Product\ShowPage\TaxonomyElement;
use Sylius\Behat\Element\Product\ShowPage\TranslationsElement;
use Sylius\Behat\Element\Product\ShowPage\VariantsElement;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.behat.element.product.show.associations', AssociationsElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.attributes', AttributesElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.details', DetailsElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.media', MediaElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.more_details', TranslationsElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.pricing', PricingElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.shipping', ShippingElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.taxonomy', TaxonomyElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.options', OptionsElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.show.variants', VariantsElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set(LowestPriceInformationElementInterface::class, LowestPriceInformationElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.product.index.vertical_menu', VerticalMenuElement::class)
        ->parent('sylius.behat.element')
    ;
};
