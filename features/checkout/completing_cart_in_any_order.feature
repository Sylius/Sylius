@checkout
Feature: Add possibility to perform checkout in any order

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
        And I use "async" checkout type
        # Temporarily specify the checkout type

    @api @no-ui
    Scenario: Completing the cart with free, not shippable item
    (without doing addressing, shipping selection, and payment selection)
        Given I have product "Sylius eBook" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        When I confirm my order
        Then the cart should be placed

    @api @no-ui
    Scenario: Completing the cart with free, shippable item
    (without doing addressing, shipping selection, and payment selection)
        Given I have product "Sylius T-Shirt" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        When I confirm my order
        Then the cart should be placed

    @api @no-ui
    Scenario: Completing the cart with paid, shippable item
    (without doing addressing, shipping selection, and payment selection)
        Given I have product "PHP Mascot" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        When I confirm my order
        Then the cart should be placed

    @api @no-ui
    Scenario: Completing the cart with paid, not shippable item
    (without doing addressing, shipping selection, and payment selection)
        Given I have product "Toyota Kata eBook" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        When I confirm my order
        Then the cart should be placed

    @api @no-ui
    Scenario: Completing the cart with free, shippable item
    (without doing addressing and payment selection)
        Given I have product "Sylius T-Shirt" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I completed the shipping step with "SHL" shipping method
        When I confirm my order
        Then the cart should be placed

    @api @no-ui
    Scenario: Completing the cart with paid, not shippable item
    (without doing addressing and shipping selection)
        Given I have product "Toyota Kata eBook" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I choose "offline" payment method
        When I confirm my order
        Then the cart should be placed

#    @api
#    Scenario: Completing the cart with address and default shipping and payment method
#    (without payment selection, addressing, and shipping selection)
#        Given I have product "Sylius eBook" in the cart
#        And I complete the addressing step

    @api @no-ui
    Scenario: Completing cart with address, selecting payment and selecting shipment (in this order)
        Given I have product "PHP Mascot" in the cart
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Patrick Jane"
        And I complete the addressing step
        And I choose "offline" payment method
        And I completed the shipping step with "SHL" shipping method
        When I confirm my order
        Then the cart should be placed
