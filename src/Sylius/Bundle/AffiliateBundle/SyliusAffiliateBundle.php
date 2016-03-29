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

use Sylius\Bundle\AffiliateBundle\DependencyInjection\Compiler\RegisterGoalProvisionsPass;
use Sylius\Bundle\AffiliateBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\AffiliateGoalInterface;
use Sylius\Component\Affiliate\Model\BannerInterface;
use Sylius\Component\Affiliate\Model\InvitationInterface;
use Sylius\Component\Affiliate\Model\ProvisionInterface;
use Sylius\Component\Affiliate\Model\RuleInterface;
use Sylius\Component\Affiliate\Model\RewardInterface;
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
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterGoalProvisionsPass());
        $container->addCompilerPass(new RegisterRuleCheckersPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            AffiliateInterface::class     => 'sylius.model.affiliate.class',
            BannerInterface::class        => 'sylius.model.affiliate_banner.class',
            AffiliateGoalInterface::class => 'sylius.model.affiliate_goal.class',
            ProvisionInterface::class     => 'sylius.model.affiliate_provision.class',
            RuleInterface::class          => 'sylius.model.affiliate_rule.class',
            InvitationInterface::class    => 'sylius.model.invitation.class',
            RewardInterface::class        => 'sylius.model.reward.class',
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
