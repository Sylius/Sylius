<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\ExpressionLanguage;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

/**
 * Adds some function to the default ExpressionLanguage.
 *
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class ExpressionLanguage extends BaseExpressionLanguage implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($expression, $values = array())
    {
        $values['container'] = $this->container;

        return parent::evaluate($expression, $values);
    }

    /**
     * {@inheritdoc}
     */
    protected function registerFunctions()
    {
        parent::registerFunctions();

        $this->register(
            'service',
            function ($arg) {
                return sprintf('$this->get(%s)', $arg);
            },
            function (array $variables, $value) {
                return $variables['container']->get($value);
            }
        );

        $this->register(
            'parameter',
            function ($arg) {
                return sprintf('$this->getParameter(%s)', $arg);
            },
            function (array $variables, $value) {
                return $variables['container']->getParameter($value);
            }
        );
    }
}
