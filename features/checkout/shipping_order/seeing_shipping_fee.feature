@checkout
Feature: Seeing detailed shipping fee on selecting shipping method page
    In order to be aware of shipping fee applied for my shipment
    As a Customer
    I want to be able to see shipping fee

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "The Sorting Hat" priced at "$19.99"
        And the store has a product "No-Face god" priced at "$19.99"
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing the shipping fee per shipment on selecting shipping method
        Given the store has "UPS" shipping method with "$20.00" fee
        And I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be on the checkout shipping step
        And I should see shipping method "UPS" with fee "$20.00"

    @ui
    Scenario: Seeing the shipping fee per unit on selecting shipping method
        Given the store has "UPS" shipping method with "$5.00" fee per unit
        And I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be on the checkout shipping step
        And I should see shipping method "UPS" with fee "$5.00"
