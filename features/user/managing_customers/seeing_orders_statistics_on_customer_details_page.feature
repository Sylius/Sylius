@customer_statistics
Feature: Seeing customer's orders' statistics
    In order to know how many orders a customer has placed, and what's their total value
    As an Administrator
    I want to be able to see orders statistics of customer on their details page

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store also operates on another channel named "Web-UK" in "GBP" currency
        And the store has a product "Onion" priced at "$200" in "Web-US" channel
        And this product is also priced at "£100" in "Web-UK" channel
        And the store has customer "lirael.clayr@abhorsen.ok"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing no statistics if a customer has not placed any orders
        When I view details of the customer "b.baggins@shire.me"
        Then I should see the customer has not placed any orders yet

    @ui
    Scenario: Seeing how many orders the customer has placed in specific channel
        Given customer "lirael.clayr@abhorsen.ok" has placed 12 orders on the "Web-UK" channel in each buying 2 "Onion" products
        When I view their details
        Then I should see that they have placed 12 orders in the "Web-UK" channel

    @ui
    Scenario: Seeing the total value of customer's orders' in given channel in its base currency
        Given customer "lirael.clayr@abhorsen.ok" has placed 5 orders on the "Web-US" channel in each buying 5 "Onion" products
        When I view their details
        Then I should see that the overall total value of all their orders in the "Web-US" channel is "$5,000.00"

    @ui
    Scenario: Seeing the average total of customer's order in given channel in its base currency
        Given customer "lirael.clayr@abhorsen.ok" has placed 12 orders on the "Web-UK" channel in each buying 2 "Onion" products
        When I view their details
        Then I should see that the average total value of their order in the "Web-UK" channel is "£2,400.00"
