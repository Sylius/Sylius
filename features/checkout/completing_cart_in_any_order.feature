@checkout
Feature: Add possibility to perform checkout in any order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP Mascot" priced at "$10.00"
        And the store has a product "Sylius T-Shirt" priced at "$0.00"
        And the store has a product "Sylius eBook" priced at "$0.00"
        And this product does not require shipping
        And the store has a product "Toyota Kata eBook" priced at "$50.00"
        And this product does not require shipping
        And the store has "SHL" shipping method with "$5.00" fee
        And the store allows paying "offline"
        And I am a logged in customer

    @api
    Scenario: Completing cart with free, not shippable item (without doing addressing, shipping selection and payment selection)
        Given I have product "Sylius eBook" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Patrick Jane"
        And I complete the addressing step
        When I confirm my order
        Then the cart should be placed

    @api
    Scenario: Completing cart with free, shippable item (without doing addressing and payment selection)
        Given I have product "Sylius T-Shirt" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Patrick Jane"
        And I complete the addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I completed the shipping step with "SHL" shipping method
        When I confirm my order
        Then the cart should be placed
