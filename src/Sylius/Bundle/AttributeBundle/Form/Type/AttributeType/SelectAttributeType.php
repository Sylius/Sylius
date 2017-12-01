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

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType;

use Sylius\Component\Attribute\Repository\AttributeSelectOptionRepositoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectAttributeType extends AbstractType
{
    /**
     * @var string
     */
    private $defaultLocaleCode;

    private $optionRepository;
    private $model_class;

    /**
     * @param TranslationLocaleProviderInterface $localeProvider
     */
    public function __construct(AttributeSelectOptionRepositoryInterface $attributeSelectOptionRepository, TranslationLocaleProviderInterface $localeProvider)
    {
        $this->defaultLocaleCode = $localeProvider->getDefaultLocaleCode();
        $this->optionRepository = $attributeSelectOptionRepository;
        $this->model_class      = $attributeSelectOptionRepository->getClassName();
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (is_array($options['configuration'])
            && isset($options['configuration']['multiple'])
            && !$options['configuration']['multiple']) {
            $builder->addModelTransformer(new CallbackTransformer(
                function ($array) {
                    if (count($array) > 0) {
                        return $array[0];
                    }

                    return null;
                },
                function ($string) {
                    if (null !== $string) {
                        return [$string];
                    }

                    return [];
                }
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $repository = $this->optionRepository;

        $resolver
            ->setRequired('configuration')
            ->setDefault('placeholder', 'sylius.form.attribute_type_configuration.select.choose')
            ->setDefault('class', $this->model_class)
            ->setRequired('attribute')
            ->setNormalizer('query_builder', function (OptionsResolver $options) use ($repository)
            {
                return $repository->getAttributeSelectOptionsQB($options["attribute"]);
            })
            ->setNormalizer('multiple', function (OptionsResolver $options) {
                if (is_array($options['configuration']) && isset($options['configuration']['multiple'])) {
                    return $options['configuration']['multiple'];
                }

                return false;
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_attribute_type_select';
    }
}
