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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\ProductAwareRuleUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class ProductDeletionListener
{
    /** @var ProductAwareRuleUpdaterInterface[] */
    private iterable $ruleUpdaters;

    public function __construct(
        private RequestStack $requestStack,
        ProductAwareRuleUpdaterInterface ...$ruleUpdaters,
    ) {
       $this->ruleUpdaters = $ruleUpdaters;
    }

    public function removeProductFromPromotionRules(GenericEvent $event): void
    {
        $product = $event->getSubject();
        Assert::isInstanceOf($product, ProductInterface::class);

        $updatedPromotionCodes = [];
        foreach ($this->ruleUpdaters as $ruleUpdater) {
            $updatedPromotionCodes[] = $ruleUpdater->updateAfterProductDeletion($product);
        }

        $updatedPromotionCodes = array_merge(...$updatedPromotionCodes);

        if ([] !== $updatedPromotionCodes) {
            $flashes = FlashBagProvider::getFlashBag($this->requestStack);
            $flashes->add('info', [
                'message' => 'sylius.promotion.update_rules',
                'parameters' => ['%codes%' => implode(', ', array_unique($updatedPromotionCodes))],
            ]);
        }
    }
}
