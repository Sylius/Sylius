@checkout
Feature: Addressing an order
    In order to address an order
    As a Guest
    I want to be able to fill addressing details

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"

    @ui
    Scenario: Address an order without different billing address
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I specify the email as "jon.snow@example.com"
        And I proceed to the next step

    @ui
    Scenario: Address an order with different billing address
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I specify the email as "eddard.stark@example.com"
        And I choose the different billing address
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Eddard Stark"
        And I proceed to the next step
