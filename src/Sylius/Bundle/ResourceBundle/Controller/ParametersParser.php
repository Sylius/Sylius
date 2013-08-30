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

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function parse(array $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->parse($value);
            }

            if (is_string($value) && 0 === strpos($value, '$')) {
                $parameters[$key] = $request->get(substr($value, 1));
            }

            if (is_string($value) && $result = $this->getServiceAndExpression($value)) {
                $accessor = PropertyAccess::createPropertyAccessor();
                try{
                    $value = $accessor->getValue($result['service'], $result['expression']);
                    $parameters[$key] = $value;
                } catch (\Exception $e) {
                    return false;
                }
            }
        }

        return $parameters;
    }

    protected function getServiceAndExpression($expression)
    {
        $expressionParts = explode('.', $expression);

        $serviceName = "";
        $serviceExists = false;
        while (count($expressionParts) > 0 && !$serviceExists = $this->container->has($serviceName))
        {
            $serviceName .= ($serviceName === "") ? array_shift($expressionParts) : '.'.array_shift($expressionParts);
        }

        if (!$serviceExists) return false;

        return array(
            'service' => $this->container->get($serviceName),
            'expression' => implode('.', $expressionParts)
        );
    }
}
