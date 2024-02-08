<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Twig\Environment;

final class CatalogPromotionScopeTypeExtension extends AbstractTypeExtension
{
    private array $scopeTypes = [];

    private array $scopeConfigurationTypes;

    public function __construct(iterable $scopeConfigurationTypes, private Environment $twig)
    {
        foreach ($scopeConfigurationTypes as $type => $formType) {
            $this->scopeConfigurationTypes[$type] = $formType::class;
            $this->scopeTypes['sylius.form.catalog_promotion.scope.' . $type] = $type;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->scopeTypes,
                'choice_attr' => function (?string $type) use ($builder): array {
                    return [
                        'data-configuration' => $this->twig->render(
                            '@SyliusAdmin/CatalogPromotion/Scope/' . $type . '.html.twig',
                            ['field' => $builder->create('configuration', $this->scopeConfigurationTypes[$type])->getForm()->createView()],
                        ),
                    ];
                },
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [CatalogPromotionScopeType::class];
    }
}
