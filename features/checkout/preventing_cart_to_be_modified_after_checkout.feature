@checkout
Feature: Preventing cart to be modified after checkout by Customer
    In order to not modify cart data after checkout
    As a Customer
    I want my cart to blocked from making changes after checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sig Sauer P226" priced at "$499.99"
        And the store has a product "AK-47" priced at "$99.99"
        And the store ships everywhere for free
        And the store ships everywhere with ups
        And the store allows paying with "Cash on Delivery"
        And the store allows paying with "Helicopter Money"
        And I am a logged in customer

    @api
    Scenario: Changing address information after completing checkout
        Given I have product "Sig Sauer P226" in the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        When I try to change the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be informed that cart is no longer available

    @api
    Scenario: Changing shipping method after completing checkout
        Given I have product "Sig Sauer P226" in the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        When I try to change shipping method to "UPS"
        Then I should be informed that cart is no longer available

    @api
    Scenario: Changing payment method after completing checkout
        Given I have product "Sig Sauer P226" in the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        When I try to change payment method to "Helicopter Money" payment
        Then I should be informed that cart is no longer available

    @api
    Scenario: Adding product after completing checkout
        Given I have product "Sig Sauer P226" in the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        When I try to add product "AK-47" to the cart
        Then I should be informed that cart is no longer available

    @api
    Scenario: Removing product after completing checkout
        Given I have product "Sig Sauer P226" in the cart
        And I have product "AK-47" in the cart
        Then I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        When I try to remove product "AK-47" from the cart
        Then I should be informed that cart is no longer available
