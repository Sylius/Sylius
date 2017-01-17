@admin_dashboard
Feature: Statistics dashboard in a single channel
    In order to have an overview of my sales
    As an Administrator
    I want to see overall statistics on my admin dashboard

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store allows paying offline
        And the store has a product "Sylius T-Shirt"
        And this product has "Red XL" variant priced at "$40"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing basic statistics for entire store
        Given 3 customers have placed 4 orders for total of "$8566.00"
        And then 2 more customers have placed 2 orders for total of "$459.00"
        When I open administration dashboard
        Then I should see 6 new orders
        And I should see 5 new customers
        And there should be total sales of "$9,025.00"
        And the average order value should be "$1,504.17"

    @ui
    Scenario: Statistics include only placed orders that were not cancelled
        Given 4 customers have placed 4 orders for total of "$5241.00"
        And 2 customers have added products to the cart for total of "$3450.00"
        And 1 customers have placed 1 orders for total of "$1000.00"
        But the customer cancelled this order
        When I open administration dashboard
        Then I should see 4 new orders
        And I should see 7 new customers
        And there should be total sales of "$5,241.00"
        And the average order value should be "$1,310.25"

    @ui
    Scenario: Seeing recent orders and customers
        Given 2 customers have placed 3 orders for total of "$340.00"
        And 2 customers have added products to the cart for total of "$424.00"
        When I open administration dashboard
        Then I should see 4 new customers in the list
        And I should see 3 new orders in the list
