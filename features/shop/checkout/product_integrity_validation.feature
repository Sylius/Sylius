@paying_for_order
Feature: Order products integrity
    In order to have valid products
    As a Customer
    I want to have enabled products in my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store has a "Super Cool T-Shirt" configurable product
        And this product has "Small", "Medium" and "Large" variants
        And this product's price is "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer

    @ui @api
    Scenario: Preventing customer from completing checkout with no longer available products
        Given I have product "PHP T-Shirt" added to the cart
        And I have proceeded through checkout process
        But the product "PHP T-Shirt" has been disabled
        When I try to confirm my order
        Then I should be informed that this product has been disabled
        And I should not see the thank you page

    @ui @api
    Scenario: Preventing customer from completing checkout with no longer available product variant
        Given I have "Small" variant of product "Super Cool T-Shirt" in the cart
        And I have proceeded selecting "Offline" payment method
        But this variant has been disabled
        When I confirm my order
        Then I should be informed that this variant has been disabled
        And I should not see the thank you page
