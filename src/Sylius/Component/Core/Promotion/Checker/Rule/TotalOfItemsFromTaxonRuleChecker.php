<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\TotalOfItemsFromTaxonConfigurationType;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TotalOfItemsFromTaxonRuleChecker implements RuleCheckerInterface, ChannelAwareRuleCheckerInterface
{
    const TYPE = 'total_of_items_from_taxon';

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(TaxonRepositoryInterface $taxonRepository)
    {
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        Assert::isInstanceOf($subject, OrderInterface::class);

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode])) {
            return false;
        }

        $configuration = $configuration[$channelCode];

        if (!isset($configuration['taxon']) || !isset($configuration['amount'])) {
            return false;
        }

        $targetTaxon = $this->taxonRepository->findOneBy(['code' => $configuration['taxon']]);
        if (null === $targetTaxon) {
            return false;
        }

        $itemsWithTaxonTotal = 0;

        /** @var OrderItemInterface $item */
        foreach ($subject->getItems() as $item) {
            if (!$item->getProduct()->filterProductTaxonsByTaxon($targetTaxon)->isEmpty()) {
                $itemsWithTaxonTotal += $item->getTotal();
            }
        }

        return $itemsWithTaxonTotal >= $configuration['amount'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return TotalOfItemsFromTaxonConfigurationType::class;
    }
}
