@checkout
Feature: Seeing detailed shipping fee on order summary page
    In order to aware of shipping fee applied for my shipment
    As a Customer
    I want to be able to see detailed shipping fee

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "The Sorting Hat" priced at "$19.99"
        And the store has a product "No-Face god" priced at "$19.99"
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing the shipping fee per shipment on order summary
        Given the store has "UPS" shipping method with "$20.00" fee
        And I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be on the checkout shipping step
        And I should see shipping method "UPS" with fee "$20.00"

    @ui
    Scenario: Seeing the shipping fee per unit on order summary
        Given the store has "UPS" shipping method with "$5.00" fee per unit
        And I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be on the checkout shipping step
        And I should see shipping method "UPS" with fee "$5.00"

    @ui
    Scenario: Seeing the flexible shipping fee on order summary
        Given the store has "UPS" shipping method with "$40.00" fee on fist unit and "$5.00" on next 10
        And I have product "The Sorting Hat" in the cart
        And I have product "No-Face god" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be on the checkout shipping step
        And I should see shipping method "UPS" with fee "$45.00"
