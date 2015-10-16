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

use Doctrine\ORM\PersistentCollection;
use Sylius\Component\Core\Promotion\Benefit\AddProductBenefit;
use Sylius\Component\Promotion\Benefit\PromotionBenefitInterface;
use Sylius\Component\Promotion\Filter\FilterInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Applies all registered promotion actions to given subject.
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

    public function __construct(ServiceRegistryInterface $benefitRegistry, ServiceRegistryInterface $filterRegistry)
    {
        $this->benefitRegistry = $benefitRegistry;
        $this->filterRegistry = $filterRegistry;
    }

    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        // preparing copy of order items for filtering (every action we start from clear set)
        $orderItems = $subject->getItems();

        foreach ($promotion->getActions() as $action) {
            $filteredSubjects = $orderItems;

            // filtering down order items for the ones that apply to benefit
            /** @var \Sylius\Component\Promotion\Model\FilterInterface $filter */
            foreach ($action->getFilters() as $filter) {
                /** @var FilterInterface $filterObject */
                $filterObject = $this->filterRegistry->get($filter->getType());

                $filteredSubjects = $filterObject->apply($filteredSubjects, $filter->getConfiguration());
            }

            // apply all of the benefits to the filtered set
            foreach ($action->getBenefits() as $benefit) {
                /** @var PromotionBenefitInterface $benefitObject */
                $benefitObject = $this->benefitRegistry->get($benefit->getType());

                /** @var  $filteredSubject */
                foreach ($filteredSubjects as $filteredSubject) {
                    // so far addProductBenefit works only on Order
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

