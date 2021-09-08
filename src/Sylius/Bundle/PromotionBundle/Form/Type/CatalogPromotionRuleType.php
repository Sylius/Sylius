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

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class CatalogPromotionRuleType extends AbstractResourceType
{
    private array $ruleTypes;

    public function __construct(string $dataClass, array $validationGroups = [], array $ruleTypes = [])
    {
        parent::__construct($dataClass, $validationGroups);

        $this->ruleTypes = $ruleTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->ruleTypes,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_rule';
    }

}
