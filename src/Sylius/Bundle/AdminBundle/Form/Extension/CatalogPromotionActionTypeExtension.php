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

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Twig\Environment;

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
        return [CatalogPromotionActionType::class];
    }
}
