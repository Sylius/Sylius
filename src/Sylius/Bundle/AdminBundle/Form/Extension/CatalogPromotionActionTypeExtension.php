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

use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionActionType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType as BaseCatalogPromotionActionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Twig\Environment;

trigger_deprecation(
    'sylius/admin-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Starting with this version, form types will be extended using the parent form like in %s.',
    CatalogPromotionActionTypeExtension::class,
    CatalogPromotionActionType::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class CatalogPromotionActionTypeExtension extends AbstractTypeExtension
{
    private array $actionTypes = [];

    private array $actionConfigurationTypes;

    public function __construct(iterable $actionConfigurationTypes, private Environment $twig)
    {
        foreach ($actionConfigurationTypes as $type => $formType) {
            $this->actionConfigurationTypes[$type] = $formType::class;
            $this->actionTypes['sylius.form.catalog_promotion.action.' . $type] = $type;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->actionTypes,
                'choice_attr' => function (?string $type) use ($builder): array {
                    return [
                        'data-configuration' => $this->twig->render(
                            '@SyliusAdmin/CatalogPromotion/_action.html.twig',
                            ['field' => $builder->create(
                                'configuration',
                                $this->actionConfigurationTypes[$type],
                                ['label' => false, 'csrf_protection' => false],
                            )->getForm()->createView()],
                        ),
                    ];
                },
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [BaseCatalogPromotionActionType::class];
    }
}
