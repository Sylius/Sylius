Specification
-------------

We strongly encourage you to follow our BDD path in implementing Sylius plugins. In fact, proper tests are one of the requirements to
(:doc:`have your plugin officially accepted</plugins/index>`).

.. attention::

    Even though we're big fans of our Behat and PHPSpec-based workflow, we do not enforce you to use the same libraries.
    We strongly believe that properly tested code is the biggest value, but everyone should feel well with their own tests.
    If you're not familiar with PHPSpec, but know PHPUnit (or anything else) by heart - keep rocking with your favorite tool!

Scenario
********

Let's start with describing how **marking a product variant available on demand** should work

.. code-block:: gherkin

    @managing_product_variants
    Feature: Marking a variant as available on demand
        In order to inform customer about possibility to order a product variant on demand
        As an Administrator
        I want to be able to mark product variant as available on demand

        Background:
            Given the store has a "Iron Man Suite" configurable product
            And this product has "Mark XLVI" variant
            And I am logged in as an administrator

        @ui
        Scenario: Marking product variant as available on demand
            When I want to modify the "Mark XLVI" product variant
            And I mark it as available on demand
            And I save my changes
            Then I should be notified that it has been successfully edited
            And this product variant should be available on demand

What is really important, usually you don't need to write the whole Behat scenario on your own! In the example above only 2 steps
would need a custom implementation. Rest of them can be easily reused from **Sylius** Behat system.

.. important::

   If you're not familiar with our BDD workflow, with Behat, take a look at
   :doc:`our BDD guide</bdd/index>`. All Behat configuration (contexts, pages, services, suites etc.) are explained
   there in details.


Behavior implementation
***********************

.. code-block:: php

    <?php

    namespace Tests\IronMan\SyliusProductOnDemandPlugin\Behat\Context\Ui\Admin;

    use Behat\Behat\Context\Context;
    use Webmozart\Assert\Assert;
    //...

    final class ManagingProductVariantsContext implements Context
    {
        /**
         * @When I mark it as available on demand
         */
        public function markVariantAsAvailableOnDemand(): void
        {
            $this->productVariantEditPage->markAsAvailableOnDemand();
        }

        /**
         * @Then this product variant should be available on demand
         */
        public function thisProductVariantShouldBeAvailableOnDemand(): void
        {
            Assert::true($this->productVariantEditPage->isAvailableOnDemand());
        }
    }

First step is done - we have a failing test, that would be green when we implement a desired functionality.
