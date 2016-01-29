<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Processor;

use Sylius\Component\Metadata\Model\MetadataInterface;
use Twig_Environment;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TwigMetadataProcessor implements MetadataProcessorInterface
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function process(MetadataInterface $metadata, array $options = [])
    {
        $metadata->forAll(function ($property) use ($options) {
            return $this->processProperty($property, $options);
        });

        return $metadata;
    }

    /**
     * @param mixed $property
     * @param array $options
     *
     * @return mixed
     */
    private function processProperty($property, array $options)
    {
        if (null === $property) {
            return null;
        }

        if ($property instanceof MetadataInterface) {
            $this->process($property, $options);

            return $property;
        }

        if (is_array($property)) {
            foreach ($property as $key => &$value) {
                $value = $this->processProperty($value, $options);
            }

            return $property;
        }

        return $this->twig->createTemplate((string) $property)->render($options);
    }
}
