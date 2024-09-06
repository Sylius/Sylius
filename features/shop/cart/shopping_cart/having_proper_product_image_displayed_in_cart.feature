@shopping_cart
Feature: Having proper product image displayed in the cart
    In order be aware of how the product that I'm buying looks like
    As a Visitor
    I want to have proper product image displayed in the cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt Car"
        And this product has "Small logo" variant priced at "$12.35"
        And this product has "Medium logo" variant priced at "$15.35"
        And this product has an image "lamborghini.jpg" with "main" type
        And this product has an image "ford.jpg" with "main" type for "Medium logo" variant

    @ui @no-api
    Scenario: Having a variant's image displayed in the cart
        When I add "Medium logo" variant of this product to the cart
        Then I should be on my cart summary page
        And 1st item in my cart should have "ford.jpg" image displayed

    @ui @no-api
    Scenario: Having a product's image displayed in the cart if variant does not have one
        When I add "Small logo" variant of this product to the cart
        Then I should be on my cart summary page
        And 1st item in my cart should have "lamborghini.jpg" image displayed
