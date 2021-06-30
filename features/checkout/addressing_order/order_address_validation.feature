@checkout
Feature: Order address validation
    In order to avoid making mistakes when addressing order
    As Visitor
    I want to be prevented from adding incorrect order address

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Stark T-shirt" priced at "$12.00"
        And the store allows paying offline
        And the store has "UPS" shipping method with "$20.00" fee

    @api
    Scenario: Trying to add address with incorrect country to the cart by the visitor
        Given the visitor has product "Stark T-Shirt" in the cart
        When the visitor specify the email as "jon.snow@example.com"
        And the visitor try to specify the incorrect billing address as "Ankh Morpork", "Frost Alley", "90210", "United Russia" for "Jon Snow"
        And the visitor completes the addressing step
        Then I should be notified that "United Russia" country does not exist

    @api
    Scenario: Trying to add address without country to the cart by the visitor
        Given the visitor has product "Stark T-Shirt" in the cart
        When the visitor specify the email as "jon.snow@example.com"
        And the visitor try to specify the billing address without country as "Ankh Morpork", "Frost Alley", "90210" for "Jon Snow"
        And the visitor completes the addressing step
        Then I should be notified that address without country cannot exist
