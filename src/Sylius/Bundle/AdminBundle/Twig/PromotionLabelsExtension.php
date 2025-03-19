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

namespace Sylius\Bundle\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PromotionLabelsExtension extends AbstractExtension
{
    /**
     * @param array<string, string> $ruleTypes
     * @param array<string, string> $actionTypes
     */
    public function __construct(
        private readonly array $ruleTypes,
        private readonly array $actionTypes,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_admin_get_promotion_action_label', [$this, 'getPromotionActionLabel']),
            new TwigFunction('sylius_admin_get_promotion_rule_label', [$this, 'getPromotionRuleLabel']),
        ];
    }

    public function getPromotionActionLabel(string $type): string
    {
        return $this->actionTypes[$type] ?? '';
    }

    public function getPromotionRuleLabel(string $type): string
    {
        return $this->ruleTypes[$type] ?? '';
    }
}
