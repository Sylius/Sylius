@customer_account
Feature: Viewing orders only from current channel
    In order to follow my orders
    As a Customer
    I want to be able to track my placed orders from current channel

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on a channel named "Web-UK" in "GBP" currency
        And the store has country "United States"
        And the store has country "United Kingdom"
        And the store has a zone "United States + United Kingdom" with code "US + UK"
        And this zone has the "United States" country member
        And this zone has the "United Kingdom" country member
        And the store has a product "Angel T-Shirt" priced at "$100" in "Web-US" channel
        And this product is also priced at "£200" in "Web-UK" channel
        And the store ships everywhere for free for all channels
        And the store allows paying offline for all channels
        And there is a customer "John Hancock" identified by an email "hancock@superheronope.com" and a password "superPower"
        And this customer has started checkout on a channel "Web-US"
        And the customer bought a single "Angel T-Shirt"
        And the customer "John Hancock" addressed it to "350 5th Ave", "10118" "New York" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Offline" payment
        And this customer has started checkout on a channel "Web-UK"
        And the customer bought a single "Angel T-Shirt"
        And the customer "Sherlock Holmes" addressed it to "221B Baker Street", "44123" "London" in the "United Kingdom" with identical billing address
        And the customer chose "Free" shipping method with "Offline" payment
        And I am logged in as "hancock@superheronope.com"

    @ui
    Scenario: Viewing orders only from current channel
        When I change my current channel to "Web-US"
        And I browse my orders
        Then I should see a single order in the list
