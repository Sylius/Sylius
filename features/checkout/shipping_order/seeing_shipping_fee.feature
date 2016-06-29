@checkout
Feature: Seeing detailed shipping fee on order summary page
    In order to aware of shipping fee applied for my shipment
    As a Customer
    I want to be able to see detailed shipping fee

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "The Sorting Hat" priced at "€19.99"
        And the store allows paying offline
        And I am a logged in customer

    @todo
    Scenario: Seeing the shipping fee per shipment on order summary
        Given the store has "UPS" shipping method with "€20.00" fee per shipment
        And I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        Then I should be on the checkout shipping step
        And I should see shipping fee "€20.00"

    @todo
    Scenario: Seeing the shipping total on order summary
        Given the store has "UPS" shipping method with "€5.00" fee per item
        And I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I proceed order with "UPS" shipping method and "Offline" payment
        Then I should be on the checkout shipping step
        And I should see shipping fee "€5.00" for every item

    @todo
    Scenario: Seeing the shipping total on order summary
        Given the store has "UPS" shipping method with "€40.00" fee on fist item and "€5.00" on next 10
        And I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I proceed order with "UPS" shipping method and "Offline" payment
        Then I should be on the checkout shipping step
        And I should see shipping fee "€40.00" on first item
        And I should see shipping fee "€5.00" on next 10 additional items

