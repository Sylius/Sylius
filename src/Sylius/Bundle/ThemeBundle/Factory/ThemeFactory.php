<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeFactory implements ThemeFactoryInterface
{
    /**
     * @var string
     */
    private $themeClassName;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param string $themeClassName
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct($themeClassName, PropertyAccessorInterface $propertyAccessor)
    {
        $this->themeClassName = $themeClassName;
        $this->propertyAccessor = $propertyAccessor;

        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver
            ->setRequired([
                'name',
                'slug',
            ])
            ->setDefined('description')
            ->setDefault('parents', [])
            ->setAllowedTypes('parents', 'array')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $themeData)
    {
        /** @var ThemeInterface $theme */
        $theme = new $this->themeClassName();

        $themeData = $this->optionsResolver->resolve($themeData);

        foreach ($themeData as $attributeKey => $attributeValue) {
            $this->propertyAccessor->setValue($theme, $this->normalizeAttributeKey($attributeKey), $attributeValue);
        }

        return $theme;
    }

    /**
     * @param string $attributeKey
     *
     * @return string
     */
    private function normalizeAttributeKey($attributeKey)
    {
        if ('parents' === $attributeKey) {
            $attributeKey = 'parentsSlugs';
        }

        return $attributeKey;
    }
}
