@checkout
Feature: Seeing province created manually on order summary page
    In order to be certain about province which I created manually
    As a Customer
    I want to be able to see province on the order summary page

    Background:
        Given the store operates on a channel named "Web"
        And the store ships to "United States"
        And the store has a zone "United States" with code "US"
        And this zone has the "United States" country member
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And I am a logged in customer

    @api @ui
    Scenario: Seeing manually defined province on order summary page
        Given I added product "PHP T-Shirt" to the cart
        And I addressed the cart with "Florida" province
        And I chose "DHL" shipping method and "Cash on Delivery" payment method
        When I check summary of my order
        And I should see "Florida" in the shipping address
        And I should see "Florida" in the billing address
