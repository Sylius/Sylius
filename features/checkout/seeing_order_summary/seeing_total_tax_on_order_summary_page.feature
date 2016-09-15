@checkout
Feature: Seeing tax total on order summary page
    In order to be certain about tax total
    As a Customer
    I want to be able to see tax total on the order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has a product "The Sorting Hat" priced at "$100.00"
        And it belongs to "Clothes" tax category
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing the total discount on order summary page
        Given I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my tax total should be "$23.00"
