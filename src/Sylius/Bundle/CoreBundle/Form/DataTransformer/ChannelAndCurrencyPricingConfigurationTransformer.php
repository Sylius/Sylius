<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ChannelAndCurrencyPricingConfigurationTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @param string $delimiter
     */
    public function __construct($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($configuration)
    {
        if (empty($configuration)) {
            return [];
        }

        Assert::isArray($configuration);

        $flatConfiguration = [];
        array_walk($configuration, function ($value, $channelCode) use (&$flatConfiguration) {
            foreach ($value as $currencyCode => $price) {
                $flatConfiguration[sprintf('%s%s%s', $channelCode, $this->delimiter, $currencyCode)] = $price;
            }
        });

        return $flatConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($flatConfiguration)
    {
        Assert::isArray($flatConfiguration);

        $configuration = [];
        foreach ($flatConfiguration as $key => $value) {
            list($channelCode, $currencyCode) = explode($this->delimiter, $key);
            $configuration[$channelCode][$currencyCode] = $value;
        }

        return $configuration;
    }
}
