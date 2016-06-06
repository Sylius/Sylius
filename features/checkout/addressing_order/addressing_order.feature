@checkout_addressing
Feature: Addressing an order
    In order to address an order
    As a Customer
    I want to be able to fill addressing details

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And I am logged in customer

    @ui
    Scenario: Address an order without different billing address
        Given I have product "PHP T-Shirt" in the cart
        And I proceed with the checkout addressing step
        When I specify the first name as "Jon"
        And I specify the last name as "Snow"
        And I specify the street as "Frost Alley"
        And I choose "France"
        And I specify the city as "Ankh Morpork"
        And I specify the postcode as "90210"
        And I proceed with the next step
        Then I should be notified that the order has been successfully addressed

    @ui
    Scenario: Address an order with different billing address
        Given I have product "PHP T-Shirt" in the cart
        And I proceed with the checkout addressing step
        When I specify the shipping address
        And I choose the different billing address
        And I specify the billing's first name as "Eddard"
        And I specify the billing's last name as "Stark"
        And I specify the billing's street as "Frost Alley"
        And I choose "France" as billing's country
        And I specify the billing's city as "Ankh Morpork"
        And I specify the billing's postcode as "90210"
        And I proceed with the next step
        Then I should be notified that the order has been successfully addressed
