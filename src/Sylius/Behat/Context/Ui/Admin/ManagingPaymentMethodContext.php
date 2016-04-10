<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingPaymentMethodContext implements Context
{
    const RESOURCE_NAME = 'payment_method';

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given /^I want to modify a (payment method "([^"]*)")$/
     */
    public function iWantToModifyAPaymentMethod(PaymentMethodInterface $paymentMethod)
    {
        $this->updatePage->open(['id' => $paymentMethod->getId()]);
    }

    /**
     * @When I rename it to :name
     */
    public function iNameItTo($name)
    {
        $this->updatePage->nameIt($name);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I choose gateway :gatewayName
     */
    public function iChooseGateway($gatewayName)
    {
        $this->updatePage->chooseGateway($gatewayName);
    }

    /**
     * @Then I should be notified about successful edition
     */
    public function iShouldBeNotifiedAboutSuccessfulEdition()
    {
        $this->notificationChecker->checkEditionNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then this payment method name should be :paymentMethodName
     */
    public function thisPaymentMethodNameShouldBe($paymentMethodName)
    {
        Assert::true(
            $this->updatePage->hasResourceValues(['name' => $paymentMethodName]),
            sprintf('Payment method %s should be renamed', $paymentMethodName)
        );
    }

    /**
     * @Then this payment method gateway should be :gatewayName
     */
    public function thisPaymentMethodGatewayShouldBe($gatewayName)
    {
        Assert::true(
            $this->updatePage->hasGateway($gatewayName),
            sprintf('Payment method should have %s gateway', $gatewayName)
        );
    }
}
