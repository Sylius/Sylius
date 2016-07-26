<?php

namespace Sylius\Bundle\ThemeBundle\Twig;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeFilesystemLoader extends \Twig_Loader_Filesystem
{
    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var TemplateNameParserInterface
     */
    private $parser;

    /**
     * @param FileLocatorInterface $locator
     * @param TemplateNameParserInterface $parser
     */
    public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser)
    {
        parent::__construct([]);

        $this->locator = $locator;
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return parent::exists((string) $name);
    }

    /**
     * {@inheritdoc}
     */
    protected function findTemplate($template, $throw = true)
    {
        $logicalName = (string) $template;

        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }

        $template = $this->parser->parse($template);
        $file = $this->locator->locate($template);

        return $this->cache[$logicalName] = $file;
    }
}
