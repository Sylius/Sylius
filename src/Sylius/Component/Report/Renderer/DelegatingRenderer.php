<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Report\Renderer;

use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Mateusz Zalewski <zaleslaw@.gmail.com>
 */
class DelegatingRenderer implements DelegatingRendererInterface
{   
    /**
     * Renderer registry.
     *
     * @var ServiceRegistryInterface
     */
    protected $registry;

    /**
     * Contructor
     *
     * @param ServiceRegistryInterface $registry
     */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    } 

    public function render(ReportInterface $subject, array $context = array())
    {
        if (null === $type = $subject->getRenderer()) {
            throw new \InvalidArgumentException('Cannot render for ReportInterface instance without renderer defined.');
        }

        $renderer = $this->registry->get($type);

        return $renderer->render($subject, $subject->getRendererConfigurations(), $context);
    }
}