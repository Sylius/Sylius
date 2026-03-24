@shopping_cart
Feature: Removing disabled products from cart
    In order to have only available products in my cart
    As a Customer
    I want disabled products to be automatically removed from my cart on page load

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store has a "Super Cool T-Shirt" configurable product
        And this product has "Small", "Medium" and "Large" variants
        And this product's price is "$19.99"
        And I am a logged in customer

    @ui
    Scenario: Disabled product is automatically removed from cart when cart page is loaded
        Given I added product "PHP T-Shirt" to the cart
        But the product "PHP T-Shirt" has been disabled
        When I see the summary of my cart
        Then my cart should be empty
        And I should be notified that some products in my cart are no longer available

    @ui
    Scenario: Disabled product variant is automatically removed from cart when cart page is loaded
        Given I have "Large" variant of product "Super Cool T-Shirt" in the cart
        But the "Large" product variant is disabled
        When I see the summary of my cart
        Then my cart should be empty
        And I should be notified that some products in my cart are no longer available

    @ui
    Scenario: Product no longer available in channel is automatically removed from cart when cart page is loaded
        Given I added product "PHP T-Shirt" to the cart
        But this product is not available in "United States" channel
        When I see the summary of my cart
        Then my cart should be empty
        And I should be notified that some products in my cart are no longer available
