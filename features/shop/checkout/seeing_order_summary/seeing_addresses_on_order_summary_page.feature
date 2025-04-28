@checkout
Feature: Seeing order addresses on order summary page
    In order to be certain about shipping and billing address
    As a Visitor
    I want to be able to see addresses on the order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lannister Coat" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"

    @api @ui
    Scenario: Seeing the same shipping and billing address on order summary
        Given I added product "Lannister Coat" to the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I should be on the checkout summary step
        And address to "Jon Snow" should be used for both shipping and billing of my order

    @api @ui
    Scenario: Seeing different shipping and billing address on order summary
        Given I added product "Lannister Coat" to the cart
        And I addressed the cart with "Eddard Stark" as the billing address and "Jon Snow" as the shipping address
        And I chose "Free" shipping method and "Cash on Delivery" payment method
        When I check summary of my order
        Then my order's billing address should be to "Eddard Stark"
        And my order's shipping address should be to "Jon Snow"
