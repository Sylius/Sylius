@worker @worker:cart @javascript
Feature: Cart isolation between consecutive requests
    In order to shop while other people shop too
    As a Customer
    I want my cart to stay mine, whoever was served just before me

    Two shoppers, one long-lived process. Their requests interleave, so a
    service that remembers the first cart hands it to the second shopper. Both
    actors live in ONE scenario on purpose - split in two they would never
    share a process and nothing would be measured.

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$20.00"
        And the store has a product "Kotlin T-Shirt" priced at "$30.00"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a user "robb@stark.com" identified by "KingInTheNorth"

    @no-api @ui
    Scenario: A second shopper's cart does not reach the first one
        Given I am logged in as "robb@stark.com"
        And I have product "PHP T-Shirt" in the cart
        When an anonymous user in another browser adds products "PHP T-Shirt" and "Kotlin T-Shirt" to the cart
        Then their cart total should be "$50.00"
        When I view my cart in the previous session
        Then my cart total should be "$20.00"
