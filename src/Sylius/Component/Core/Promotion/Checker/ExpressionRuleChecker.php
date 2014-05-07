<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker;

use Sylius\Component\Core\ExpressionLanguage\ExpressionLanguageFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * Evaluates the expression stored in the configuration.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ExpressionRuleChecker implements RuleCheckerInterface
{
    /**
     * @var ExpressionLanguageFactoryInterface
     */
    protected $expressionLanguageFactory;

    /**
     * @param ExpressionLanguageFactoryInterface $expressionLanguageFactory
     */
    public function __construct(ExpressionLanguageFactoryInterface $expressionLanguageFactory)
    {
        $this->expressionLanguageFactory = $expressionLanguageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        if (!array_key_exists('expr', $configuration)) {
            return false;
        }

        $context = array('order' => $subject, 'cart' => $subject);

        if (null !== $user = $subject->getUser()) {
            $context['user'] = $user;
        }

        return $this->expressionLanguageFactory->create()->evaluate($configuration['expr'], $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_expression_configuration';
    }
}
