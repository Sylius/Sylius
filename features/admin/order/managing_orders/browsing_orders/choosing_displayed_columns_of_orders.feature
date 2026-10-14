@managing_orders
Feature: Choosing the displayed columns of orders
    In order to focus on the information I need
    As an Administrator
    I want to be able to choose which columns of the orders grid are displayed

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Green Arrow"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a customer "oliver@teamarrow.com" that placed an order
        And the customer bought a single "Green Arrow"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Hiding a column
        Given I am browsing orders
        When I hide the "Total" column
        Then I should not see the "Total" column
        But I should see the "Number" column

    @no-api @ui @mink:chromedriver
    Scenario: Hiding several columns at once
        Given I am browsing orders
        When I hide the "Total" and "Channel" columns
        Then I should not see the "Total" column
        And I should not see the "Channel" column
        But I should see the "Number" column

    @no-api @ui
    Scenario: Showing a hidden column again
        Given I am browsing orders
        When I hide the "Total" column
        And I show the "Total" column
        Then I should see the "Total" column

    @no-api @ui
    Scenario: Keeping the hidden columns when sorting orders
        Given I am browsing orders
        When I hide the "Total" column
        And I switch the way orders are sorted by number
        Then I should not see the "Total" column
        And I should see an order with "#000000001" number
