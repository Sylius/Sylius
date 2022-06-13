@checkout
Feature: Accessing order right after completing checkout
    In order to check my order right after I have placed it
    As a Customer
    I want to access this order from the thank you page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store allows paying "Cash on delivery"
        And the store ships everywhere for Free
        And there is a user "john@example.com"
        And I am logged in as "john@example.com"

    @ui @api
    Scenario: Being able to access my order right after completing checkout
        Given I added product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceeded with "Free" shipping method and "Cash on delivery" payment
        When I confirm my order
        Then I should be able to access this order's details
