<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle;

use Sylius\Bundle\AffiliateBundle\DependencyInjection\Compiler\RegisterGoalActionsPass;
use Sylius\Bundle\AffiliateBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class SyliusAffiliateBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterGoalActionsPass());
        $container->addCompilerPass(new RegisterRuleCheckersPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Affiliate\Model\ActionInterface'      => 'sylius.model.affiliate_action.class',
            'Sylius\Component\Affiliate\Model\AffiliateInterface'   => 'sylius.model.affiliate.class',
            'Sylius\Component\Affiliate\Model\GoalInterface'        => 'sylius.model.affiliate_goal.class',
            'Sylius\Component\Affiliate\Model\RuleInterface'        => 'sylius.model.affiliate_rule.class',
            'Sylius\Component\Affiliate\Model\InvitationInterface'  => 'sylius.model.invitation.class',
            'Sylius\Component\Affiliate\Model\TransactionInterface' => 'sylius.model.transaction.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Affiliate\Model';
    }
}
