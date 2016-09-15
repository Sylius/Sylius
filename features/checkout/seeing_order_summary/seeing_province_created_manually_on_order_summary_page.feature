@checkout
Feature: Seeing province created manually on order summary page
    In order to be certain about province which I created manually
    As a Customer
    I want to be able to see province on the order summary page

    Background:
        Given the store operates on a channel named "Web"
        And the store ships to "United Kingdom"
        And the store has a zone "English" with code "EN"
        And this zone has the "United Kingdom" country member
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "EN" zone
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And I am a logged in customer

    @ui @javascript
    Scenario: Seeing manually defined province on order summary page
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        When I specify the shipping address for "Jon Snow" from "Ankh Morpork", "90210", "London", "United Kingdom", "East of England"
        And I specify the billing address for "Jon Snow" from "Ankh Morpork", "90210", "London", "United Kingdom", "East of England"
        And I complete the addressing step
        And I proceed with "DHL" shipping method and "Cash on Delivery" payment
        Then I should be on the checkout summary step
        And I should see "East of England" in the shipping address
        And I should see "East of England" in the billing address
