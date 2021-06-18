@checkout
Feature: Changing address during checkout
    In order to place an order with the correct address
    As a Visitor
    I want to be able to change the address during checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "$12.54"

    @api
    Scenario: Changing address
        Given the visitor has product "T-shirt banana" in the cart
        And the visitor has specified the email as "jon.snow@example.com"
        And the visitor has specified address as "Los Angeles", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the visitor has completed the addressing step
        When the visitor changes the billing address to "Los Angeles", "Avenue", "90210", "United States" for "Jon Snow"
        And the visitor completes the addressing step
        Then address "Jon Snow", "Avenue", "90210", "Los Angeles", "United States" should be filled as billing address
        And address "Jon Snow", "Avenue", "90210", "Los Angeles", "United States" should be filled as shipping address
        And store should contain only "Jon Snow", "Avenue", "90210", "Los Angeles", "United States" address
