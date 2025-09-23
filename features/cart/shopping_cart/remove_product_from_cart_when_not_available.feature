@shopping_cart
Feature: Remove product from cart when not available
    In order to avoid checkout of unavailable products
    As a Customer
    I want to be notified when a product in my cart is no longer available
    And I want the unavailable product to be removed from my cart

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And the store has a product "PHP T-Shirt" priced at "$10.99"
        And I add this product to the cart
        And the store has a product "PHP Mug" priced at "$5.99"
        And I add this product to the cart

    @ui
    Scenario: Removing product from cart when it is disabled
        Given product "PHP Mug" has been disabled
        When I am on the summary of my cart page
        And there should be one item in my cart
        And this item should have name "PHP T-Shirt"

    @ui
    Scenario: Removing product from cart when it is not available in channel
        Given product "PHP Mug" has been disabled in channel "United States"
        When I am on the summary of my cart page
        And there should be one item in my cart
        And this item should have name "PHP T-Shirt"

    @ui
    Scenario: Removing product from cart when it is not available in channel and is disabled
        Given product "PHP Mug" has been disabled in channel "United States"
        And product "PHP Mug" has been disabled
        When I am on the summary of my cart page
        And there should be one item in my cart
        And this item should have name "PHP T-Shirt"
