<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * TemplateNameParser converts template names from the short notation
 * "@Bundle/Section/template.format.engine" to TemplateReferenceInterface instances.
 */
final class TemplateNameParser implements TemplateNameParserInterface
{
    /**
     * @var TemplateNameParserInterface
     */
    private $decoratedParser;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var array|TemplateReferenceInterface[]
     */
    private $cache = [];

    /**
     * @param TemplateNameParserInterface $decoratedParser
     * @param KernelInterface $kernel
     */
    public function __construct(TemplateNameParserInterface $decoratedParser, KernelInterface $kernel)
    {
        $this->decoratedParser = $decoratedParser;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($name): TemplateReferenceInterface
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (!preg_match('/^(?:@([^\/]*)|)(?:\/(.+))?\/(.+)\.([^\.]+)\.([^\.]+)$/', $name, $matches)) {
            return $this->decoratedParser->parse($name);
        }

        $template = new TemplateReference(
            $matches[1] ? $matches[1] . 'Bundle' : '',
            $matches[2],
            $matches[3],
            $matches[4],
            $matches[5]
        );

        if ($template->get('bundle')) {
            try {
                $this->kernel->getBundle($template->get('bundle'));
            } catch (\Exception $e) {
                return $this->decoratedParser->parse($name);
            }
        }

        return $this->cache[$name] = $template;
    }
}
