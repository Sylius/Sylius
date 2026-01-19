@checkout
Feature: Seeing shipping methods which category is same as category of all my units
    In order to select correct shipping method for my order
    As a Customer
    I want to be able to choose only shipping methods that match shipping category of all my items

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over-sized" and "Standard" shipping category
        And the store has a product "Star Trek Ship" priced at "$19.99"
        And this product belongs to "Over-sized" shipping category
        And the store has a product "Picasso T-Shirt" priced at "$19.99"
        And this product belongs to "Standard" shipping category
        And the store has a product "Rocket T-Shirt" priced at "$20.00"
        And this product belongs to "Standard" shipping category
        And the store has a product "T-Shirt banana"
        And this product has option "Size" with values "S" and "M"
        And this product is available in "S" size priced at "$12.54"
        And this product is available in "M" size priced at "$12.30"
        And the store has "Raven Post" shipping method with "$10.00" fee
        And this shipping method requires that all units match to "Standard" shipping category
        And the store has "Invisible Post" shipping method with "$30.00" fee
        And this shipping method requires that all units match to "Over-sized" shipping category
        And I am a logged in customer

    @api @ui
    Scenario: Seeing only shipping method which category is same as categories of all my units
        Given I added product "Rocket T-Shirt" to the cart
        And I added product "Picasso T-Shirt" to the cart
        And I addressed the cart
        When I go to the shipping step
        Then I should see "Raven Post" shipping method
        And I should not see "Invisible Post" shipping method

    @api @ui
    Scenario: Seeing shipping method which category is same as category of my unit
        Given I added product "Star Trek Ship" to the cart
        And I addressed the cart
        When I go to the shipping step
        Then I should see "Invisible Post" shipping method
        And I should not see "Raven Post" shipping method

    @api @ui
    Scenario: Seeing no shipping methods if my units matches to different shipping categories
        Given I added product "Rocket T-Shirt" to the cart
        And I added product "Star Trek Ship" to the cart
        And I addressed the cart
        When I go to the shipping step
        Then there should be information about no available shipping methods

    @api @ui
    Scenario: Seeing no shipping methods if not all variants of my units has same shipping category
        Given the "T-Shirt banana" product's "S" size belongs to "Standard" shipping category
        And the "T-Shirt banana" product's "M" size belongs to "Over-sized" shipping category
        And I added product "T-Shirt banana" with product option "Size" S to the cart
        And I added product "T-Shirt banana" with product option "Size" M to the cart
        And I addressed the cart
        When I go to the shipping step
        Then there should be information about no available shipping methods

    @api @ui
    Scenario: Seeing shipping methods if all variants of my units has same shipping category
        Given the "T-Shirt banana" product's "M" size belongs to "Standard" shipping category
        And the "T-Shirt banana" product's "S" size belongs to "Standard" shipping category
        And I added product "T-Shirt banana" with product option "Size" S to the cart
        And I added product "T-Shirt banana" with product option "Size" M to the cart
        And I addressed the cart
        When I go to the shipping step
        Then I should see "Raven Post" shipping method
        And I should not see "Invisible Post" shipping method
