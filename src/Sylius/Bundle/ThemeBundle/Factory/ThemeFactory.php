<?php

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeFactory implements ThemeFactoryInterface
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
                'logical_name',
            ])
            ->setDefined([
                'description',
            ])
            ->setDefaults([
                'parents' => [],
            ])
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

        foreach ($themeData as $attributeKey => $attributeValue)
        {
            $attributeKey = $this->normalizeAttributeKey($attributeKey);

            try {
                $this->propertyAccessor->setValue($theme, $attributeKey, $attributeValue);
            } catch (NoSuchPropertyException $exception) {
                // Ignore properties that does not exist in given theme model.
            }
        }

        return $theme;
    }

    /**
     * @param string $attributeKey
     *
     * @return string
     */
    protected function normalizeAttributeKey($attributeKey)
    {
        if ('parents' === $attributeKey) {
            $attributeKey = 'parentsNames';
        }

        return $attributeKey;
    }
}