@admin_dashboard
Feature: Statistics dashboard per channel
    In order to have an overview of my sales
    As an Administrator
    I want to see overall statistics on my admin dashboard in a specific channel

    Background:
        Given the store operates on a channel named "Poland"
        And there is product "Onion" available in this channel
        And the store operates on another channel named "United States"
        And there is product "Banana" available in that channel
        And the store ships everywhere for free
        And the store allows paying offline
        And I am logged in as an administrator

    @ui
    Scenario: Seeing basic statistics for the first channel by default
        Given 3 customers have placed 4 orders for total of "$8566.00" mostly "Onion" product
        And then 2 more customers have placed 2 orders for total of "$459.00" mostly "Banana" product
        When I open administration dashboard
        Then I should see 4 new orders
        And I should see 5 new customers
        And there should be total sales of "$8,566.00"
        And the average order value should be "$2,141.50"

    @ui
    Scenario: Changing viewing channel in administration dashboard
        Given 4 customers have placed 4 orders for total of "$5241.00" mostly "Onion" product
        And then 2 more customers have placed 2 orders for total of "$459.00" mostly "Banana" product
        When I open administration dashboard
        And I choose "United States" channel
        Then I should see 2 new orders
        And I should see 6 new customers
        And there should be total sales of "$459.00"
        And the average order value should be "$229.50"

    @ui
    Scenario: Seeing recent orders in a specific channel
        Given 3 customers have placed 4 orders for total of "$8566.00" mostly "Onion" product
        And then 2 more customers have placed 2 orders for total of "$459.00" mostly "Banana" product
        When I open administration dashboard for "Poland" channel
        Then I should see 4 new orders in the list

    @ui
    Scenario: Seeing recent orders in a specific channel
        Given 3 customers have placed 4 orders for total of "$8566.00" mostly "Onion" product
        And then 2 more customers have placed 2 orders for total of "$459.00" mostly "Banana" product
        When I open administration dashboard for "United States" channel
        Then I should see 2 new orders in the list
