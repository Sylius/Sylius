<?php

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

    private Environment $twig;

    public function __construct(iterable $scopeConfigurationTypes, Environment $twig)
    {
        foreach ($scopeConfigurationTypes as $type => $formType) {
            $this->scopeConfigurationTypes[$type] = get_class($formType);
            $this->scopeTypes['sylius.form.catalog_promotion.scope.'.$type] = $type;
        }

        $this->twig = $twig;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->scopeTypes,
                'choice_attr' => function(?string $type) use ($builder): array {
                    return [
                        'data-configuration' =>
                            $this->twig->render(
                                '@SyliusAdmin/CatalogPromotion/Scope/'.$type.'.html.twig',
                                ['field' => $builder->create('configuration', $this->scopeConfigurationTypes[$type])->getForm()->createView()]
                            )
                    ];
                }
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [CatalogPromotionScopeType::class];
    }
}
