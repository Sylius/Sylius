@paying_for_order
Feature: Order products integrity
    In order to have valid products
    As a Customer
    I want to have enabled products in my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Preventing customer from completing checkout with no longer available products
        Given I have product "PHP T-Shirt" in the cart
        And I have proceeded selecting "Offline" payment method
        But this product has been disabled
        When I confirm my order
        Then I should be informed that this product has been disabled
        And I should not see the thank you page
