@checkout
Feature: Seeing shipping methods which category is not same as any category of all my units
    In order to select correct shipping method for my order
    As a Customer
    I want to be able to choose shipping method which category is not same as any category of all my units

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over-sized" shipping category
        And the store has a product "Star Trek Ship" priced at "$19.99"
        And this product belongs to "Over-sized" shipping category
        And the store has a product "Picasso T-Shirt" priced at "$19.99"
        And the store has a product "T-Shirt banana"
        And this product has option "Size" with values "S" and "M"
        And this product is available in "S" size priced at "$12.54"
        And this product is available in "M" size priced at "$12.30"
        And the store has "Invisible Post" shipping method with "$30.00" fee
        And this shipping method requires that no units match to "Over-sized" shipping category
        And I am a logged in customer

    @api @ui
    Scenario: Seeing shipping methods when all of my products fit the shipping category
        Given I added product "Picasso T-Shirt" to the cart
        And I addressed the cart
        When I go to the shipping step
        Then I should be on the checkout shipping step
        And I should see "Invisible Post" shipping method

    @api @ui
    Scenario: Seeing no shipping methods when all of my products are excluded from the shipping category
        Given I added product "Star Trek Ship" to the cart
        And I addressed the cart
        When I go to the shipping step
        Then there should be information about no available shipping methods

    @api @ui
    Scenario: Seeing no shipping methods when any of my products is excluded from the shipping category
        Given I added product "Picasso T-Shirt" to the cart
        And I added product "Star Trek Ship" to the cart
        And I addressed the cart
        When I go to the shipping step
        Then there should be information about no available shipping methods

    @api @ui
    Scenario: Seeing no shipping methods when any of my variants is excluded from the shipping category
        Given the "T-Shirt banana" product's "S" size belongs to "Over-sized" shipping category
        And I added product "T-Shirt banana" with product option "Size" S to the cart
        And I added product "T-Shirt banana" with product option "Size" M to the cart
        And I addressed the cart
        When I go to the shipping step
        Then there should be information about no available shipping methods

    @api @ui
    Scenario: Seeing shipping methods when all of my variants fit the shipping category
        Given I added product "T-Shirt banana" with product option "Size" S to the cart
        And I added product "T-Shirt banana" with product option "Size" M to the cart
        And I addressed the cart
        When I go to the shipping step
        Then I should be on the checkout shipping step
        And I should see "Invisible Post" shipping method
