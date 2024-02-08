@checkout
Feature: Preventing cart from being modified after checkout
    In order to have order immutable after checkout
    As a Customer
    I don't want to be able to modify placed order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sig Sauer P226" priced at "$499.99"
        And the store has a product "AK-47" priced at "$99.99"
        And the store ships everywhere for Free
        And the store ships everywhere with "UPS"
        And the store allows paying with "Cash on Delivery"
        And the store also allows paying with "Helicopter Money"
        And I am a logged in customer

    @api
    Scenario: Preventing from changing billing address after checkout
        Given I added product "Sig Sauer P226" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Cash on Delivery" payment
        And I confirmed my order
        When I try to change the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be informed that cart is no longer available

    @api
    Scenario: Preventing from changing shipping method after checkout
        Given I added product "Sig Sauer P226" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Cash on Delivery" payment
        And I confirmed my order
        When I try to change shipping method to "UPS"
        Then I should be informed that cart is no longer available

    @api
    Scenario: Preventing from adding product after checkout
        Given I added product "Sig Sauer P226" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Cash on Delivery" payment
        And I confirmed my order
        When I try to add product "AK-47" to the cart
        Then I should be informed that cart items are no longer available

    @api
    Scenario: Preventing from removing product after checkout
        Given I added product "Sig Sauer P226" to the cart
        And I added product "AK-47" to the cart
        Then I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Cash on Delivery" payment
        And I confirmed my order
        When I try to remove product "AK-47" from the cart
        Then I should be informed that cart items are no longer available

    @api
    Scenario: Preventing from changing quantity of product after checkout
        Given I added product "Sig Sauer P226" to the cart
        And I added product "AK-47" to the cart
        Then I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Cash on Delivery" payment
        And I confirmed my order
        When I try to change product "Sig Sauer P226" quantity to 2 in my cart
        Then I should be informed that cart items are no longer available
