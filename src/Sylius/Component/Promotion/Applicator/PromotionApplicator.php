<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Promotion\Benefit\AddProductBenefit;
use Sylius\Component\Promotion\Benefit\PromotionBenefitInterface;
use Sylius\Component\Promotion\Filter\PromotionFilterInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * Applies all registered promotion actions to given subject.
 *
 * TODO: This needs to consider coupling. It's written at the Component level, but expecting items.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author  Piotr Walków <walkow.piotr@gmail.com>
 */
class PromotionApplicator implements PromotionApplicatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $benefitRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    protected $filterRegistry;

    /**
     * @param ServiceRegistryInterface $benefitRegistry
     * @param ServiceRegistryInterface $filterRegistry
     */
    public function __construct(ServiceRegistryInterface $benefitRegistry, ServiceRegistryInterface $filterRegistry)
    {
        $this->benefitRegistry = $benefitRegistry;
        $this->filterRegistry = $filterRegistry;
    }

    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface $promotion
     */
    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        // Each different action for a promotion results in it's own subjects for the promotion
        // These can be filtered down depending on how it's configured.
        $orderItems = $subject->getItems();

        if (is_object($orderItems)) {
            // Therefore we need to make sure we've got an array or PHP will pass a Collection by reference
            // and we won't get a 'fresh' copy
            $orderItems = $orderItems->toArray();
        }

        foreach ($promotion->getActions() as $action) {
            // Back to a Collection to satisfy method requirements
            $filteredSubjects = new ArrayCollection($orderItems);

            // Filter down order items for the ones that the benefit applies to
            foreach ($action->getFilters() as $filter) {
                /** @var PromotionFilterInterface $filterObject */
                $filterObject = $this->filterRegistry->get($filter->getType());
                $filteredSubjects = $filterObject->apply($filteredSubjects, $filter->getConfiguration());
            }

            // Apply all of the benefits to the filtered set
            foreach ($action->getBenefits() as $benefit) {
                /** @var PromotionBenefitInterface $benefitObject */
                $benefitObject = $this->benefitRegistry->get($benefit->getType());

                foreach ($filteredSubjects as $filteredSubject) {
                    // TODO: Coupling here?
                    // AddProductBenefit works only on Order
                    if ($benefitObject instanceof AddProductBenefit) {
                        $benefitObject->execute($subject, $benefit->getConfiguration(), $promotion);
                    } else {
                        $benefitObject->execute($filteredSubject, $benefit->getConfiguration(), $promotion);
                    }
                }
            }
        }

        $subject->addPromotion($promotion);
    }

    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface $promotion
     */
    public function revert(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        foreach ($promotion->getActions() as $action) {
            foreach ($action->getBenefits() as $benefit) {
                $this->benefitRegistry
                    ->get($benefit->getType())
                    ->revert($subject, $benefit->getConfiguration(), $promotion)
                ;
            }
        }

        $subject->removePromotion($promotion);
    }
}

