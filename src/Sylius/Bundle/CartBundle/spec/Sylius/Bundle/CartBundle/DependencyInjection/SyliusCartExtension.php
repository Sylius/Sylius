<?php

namespace spec\Sylius\Bundle\CartBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;
use Symfony\Component\Yaml\Parser;

/**
 * Sylius cart extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCartExtension extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\DependencyInjection\SyliusCartExtension');
    }

    function it_should_be_container_extension()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\DependencyInjection\Extension');
    }

    private function getEmptyConfig()
    {
        $yaml = <<<EOF
driver: doctrine/orm
resolver: sylius_cart.resolver
classes:
    cart:
        model: Cart
    item:
        model: Item
EOF;

        $parser = new Parser();

        return array($parser->parse($yaml));
    }
}
