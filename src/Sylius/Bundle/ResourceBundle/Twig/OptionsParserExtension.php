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

namespace Sylius\Bundle\ResourceBundle\Twig;

use Sylius\Bundle\ResourceBundle\Grid\Parser\OptionsParserInterface;

final class OptionsParserExtension extends \Twig_Extension
{
    /**
     * @var OptionsParserInterface
     */
    private $optionsParser;

    /**
     * @param OptionsParserInterface $optionsParser
     */
    public function __construct(OptionsParserInterface $optionsParser)
    {
        $this->optionsParser = $optionsParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('sylius_options_parse', [$this->optionsParser, 'parseOptions'], ['is_safe' => ['html']]),
        ];
    }
}
