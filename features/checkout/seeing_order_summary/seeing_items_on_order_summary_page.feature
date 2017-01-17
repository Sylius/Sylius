@checkout
Feature: Seeing order items on order summary page
    In order be certain about order content
    As a Customer
    I want to be able to see order items on the order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lannister Coat" priced at "$19.99"
        And the store has a product "Targaryen Jacket" priced at "$39.99"
        And the store has a product "Stark T-Shirt" priced at "$21.50"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing order items and theirs quantity on the order summary page
        Given I have 4 products "Lannister Coat" in the cart
        And I have 2 products "Targaryen Jacket" in the cart
        And I have 3 products "Stark T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And I should have 4 "Lannister Coat" products in the cart
        And I should have 2 "Targaryen Jacket" products in the cart
        And I should have 3 "Stark T-Shirt" products in the cart
