@checkout
Feature: Performing checkout in any order
    In order to create checkout flow with steps in any order
    And to not execute steps skipping
    As a Customer
    I want to perform checkout in any order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP Mascot" priced at "$15.00"
        And the store has a product "Sylius T-Shirt" priced at "$0.00"
        And the store has a product "Toyota Kata eBook" priced at "$50.00"
        And this product does not require shipping
        And the store has a product "Sylius eBook" priced at "$0.00"
        And this product does not require shipping
        And the store has "SHL" shipping method with "$5.00" fee
        And the store allows paying "offline"
        And I am a logged in customer

    @api @no-ui
    Scenario: Completing the cart with free, not shippable item
        Given I have product "Sylius eBook" in the cart
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I confirm my order
        Then the order should be placed

    @api @no-ui
    Scenario: Completing the cart with free, shippable item
        Given I have product "Sylius T-Shirt" in the cart
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I confirm my order
        Then the order should be placed

    @api @no-ui
    Scenario: Completing the cart with paid, shippable item
        Given I have product "PHP Mascot" in the cart
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I confirm my order
        Then the order should be placed

    @api @no-ui
    Scenario: Completing the cart with paid, not shippable item
        Given I have product "Toyota Kata eBook" in the cart
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I confirm my order
        Then the order should be placed

    @api @no-ui
    Scenario: Completing the cart with free, shippable item
        Given I have product "Sylius T-Shirt" in the cart
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with "SHL" shipping method
        And I confirm my order
        Then the order should be placed

    @api @no-ui
    Scenario: Completing the cart with paid, not shippable item
        Given I have product "Toyota Kata eBook" in the cart
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I choose "offline" payment method
        And I confirm my order
        Then the order should be placed

    @api @no-ui
    Scenario: Completing cart with address, selecting payment and selecting shipment
        Given I have product "PHP Mascot" in the cart
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Patrick Jane"
        And I complete the addressing step
        And I choose "offline" payment method
        And I proceed with "SHL" shipping method
        And I confirm my order
        Then the order should be placed
