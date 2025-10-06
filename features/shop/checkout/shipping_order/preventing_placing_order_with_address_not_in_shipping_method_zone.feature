@checkout
Feature: Preventing placing an order with address not in shipping method zone
    In order to have my order shipped without issues
    As a Visitor
    I want to be prevented from placing an order with address not being in shipping method zone

    Background:
        Given the store operates on a single channel in the "United States" named "UK Web Store"
        And the store ships to "United Kingdom"
        And the store ships to "Spain"
        And the store has a product "Ubi T-Shirt" priced at "$19.99"
        And the store has a zone "United Kingdom" with code "UK"
        And this zone has the "United Kingdom" country member
        And the store has a zone "Spain" with code "ES"
        And this zone has the "Spain" country member
        And the store has "DHL" shipping method with "$10.00" fee within the "UK" zone
        And the store allows paying "Offline"

    @api @ui
    Scenario: Being prevented from placing an order with a shipping address that's not in the shipping method zone after completing the shipping method choice step
        Given I added product "Ubi T-Shirt" to the cart
        And I have completed addressing step with email "guest@example.com" and "United Kingdom" based billing address
        And I have proceeded order with "DHL" shipping method and "Offline" payment
        But this shipping method has changed zone to "ES" zone
        When I try to complete checkout
        Then I should not be able to confirm order because products do not fit "DHL" requirements
        And I should not see the thank you page
