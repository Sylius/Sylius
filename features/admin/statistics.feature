@admin_dashboard
Feature: Statistics
    In order to gain insight into my sales performance and customers activity
    As an Administrator
    I want to view comprehensive statistics

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And the store has a product "Sylius T-Shirt"
        And this product has "Red XL" variant priced at "$40.00"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Seeing statistics for the current year and default channel when expectations are not specified
        Given it is "last day of December last year" now
        And 2 new customers have fulfilled 2 orders placed for total of "$1,000.00"
        And it is "first day of January this year" now
        And 3 new customers have fulfilled 4 orders placed for total of "$2,000.21"
        And it is "first day of February this year" now
        And 2 more new customers have paid 2 orders placed for total of "$5,000.37"
        When I view statistics
        Then I should see 5 new customers
        And I should see 6 paid orders
        And there should be total sales of "$7,000.58"
        And the average order value should be "$1,166.76"

    @api @ui @javascript
    Scenario: Seeing statistics for the previous year
        Given it is "first day of January last year" now
        And 3 new customers have fulfilled 2 orders placed for total of "$2,000.00"
        And it is "first day of February this year" now
        And 4 more new customers have paid 5 orders placed for total of "$5,000.37"
        And 2 more new customers have paid 2 orders placed for total of "$5,000.37"
        When I view statistics for "United States" channel and previous year split by month
        Then I should see 3 new customers
        And I should see 2 paid orders
        And there should be total sales of "$2,000.00"
        And the average order value should be "$1,000.00"

    @ui @javascript @no-api
    Scenario: Seeing statistics for the next year
        Given it is "first day of January last year" now
        And 3 new customers have fulfilled 2 orders placed for total of "$2,000.00"
        And it is "first day of February this year" now
        And 4 more new customers have paid 5 orders placed for total of "$5,000.37"
        And 2 more new customers have paid 2 orders placed for total of "$5,000.37"
        When I view statistics for "United States" channel and previous year split by month
        And I view statistics for "United States" channel and next year split by month
        Then I should see 6 new customers
        And I should see 7 paid orders
        And there should be total sales of "$10,000.74"
        And the average order value should be "$1,428.68"

    @api @ui @javascript
    Scenario: Seeing statistics that include only fulfilled orders that were not cancelled
        Given 4 new customers have fulfilled 4 orders placed for total of "$5,241.00"
        And 2 more new customers have placed 2 orders for total of "$459.00"
        And 2 new customers have added products to the cart for total of "$3,450.00"
        And a single customer has placed an order for total of "$1,000.00"
        But the customer cancelled this order
        When I view statistics for "United States" channel and current year split by month
        Then I should see 4 paid orders
        And I should see 9 new customers
        And there should be total sales of "$5,241.00"
        And the average order value should be "$1,310.25"
