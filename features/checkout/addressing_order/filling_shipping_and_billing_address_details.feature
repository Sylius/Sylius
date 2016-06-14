@checkout
Feature: Addressing an order
    In order to address an order
    As a Customer
    I want to be able to fill addressing details

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And I am logged in customer

    @ui
    Scenario: Address an order without different billing address
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @ui
    Scenario: Address an order with different billing address
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I choose the different billing address
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Eddard Stark"
        And I complete the addressing step
        Then I should be on the checkout shipping step
