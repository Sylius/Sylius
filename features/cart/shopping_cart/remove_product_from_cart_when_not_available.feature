@shopping_cart
Feature: Remove product from cart when not available
    In order to avoid checkout of unavailable products
    As a shop user
    I want to be notified when a product in my cart is no longer available

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Notifying about unavailable product in cart
        Given I am a logged in customer
        And the store has a product "PHP T-Shirt" priced at "$10.99"
        And I add this product to the cart
        And the store has a product "PHP Mug" priced at "$5.99"
        And I add this product to the cart
        And the "PHP Mug" product is disabled
        When I am on the summary of my cart page
        And there should be one item in my cart
        And this item should have name "PHP T-Shirt"

    @ui
    Scenario: Product is available in cart
        Given I am a logged in customer
        And the store has a product "PHP T-Shirt" priced at "$10.99"
        And I add this product to the cart
        When I am on the summary of my cart page
        And there should be one item in my cart
        And this item should have name "PHP T-Shirt"
