@checkout
Feature: Seeing order addresses on order summary page when shipping is the required one for the channel
    In order to be certain about shipping and billing address
    As a Customer
    I want to be able to see proper addresses on the order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And its required address in the checkout is shipping
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer

    @api @ui
    Scenario: Seeing the same shipping and billing address on order summary
        Given I added product "PHP T-Shirt" to the cart
        And I addressed the cart with "Jon Snow" as the shipping address
        And I chose "Free" shipping method and "Cash on Delivery" payment method
        When I check summary of my order
        Then address to "Jon Snow" should be used for both shipping and billing of my order

    @api @ui
    Scenario: Seeing different shipping and billing addresses on order summary
        Given I added product "PHP T-Shirt" to the cart
        And I addressed the cart with "Eddard Stark" as the billing address and "Jon Snow" as the shipping address
        And I chose "Free" shipping method and "Cash on Delivery" payment method
        When I check summary of my order
        Then my order's shipping address should be to "Jon Snow"
        And my order's billing address should be to "Eddard Stark"
