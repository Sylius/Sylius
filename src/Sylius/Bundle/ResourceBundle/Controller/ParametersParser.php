<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Configuration parameters parser.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class ParametersParser
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function parse(array $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->parse($value);
            }

            if (is_string($value) && 0 === strpos($value, '$')) {
                $parameters[$key] = $this->container->get('request')->get(substr($value, 1));
            }

            if (is_string($value) && $result = $this->getServiceAndExpression($value)) {
                $value = $this->propertyAccessor->getValue($result['service'], $result['expression']);
                $parameters[$key] = $value;
            }
        }

        return $parameters;
    }

    protected function getServiceAndExpression($expression)
    {
        $parts = explode(':', $expression);
        if(count($parts) != 2) return false;

        return array(
            'service' => $this->container->get($parts[0]),
            'expression' => $parts[1]
        );
    }
}
