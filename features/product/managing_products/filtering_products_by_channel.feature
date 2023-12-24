@managing_products
Feature: Filtering products by a channel
    In order to get a more focused view while managing some channel's products
    As an Administrator
    I want to be able to filter products by channel

    Background:
        Given the store operates on a channel named "Web-EU"
        And the store also operates on a channel named "Web-US"
        And the store has a product "MacBook Air" in channel "Web-EU"
        And the store also has a product "MacBook Pro" in channel "Web-EU"
        And the store also has a product "HP Spectre" in channel "Web-US"
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering products by a chosen channel
        When I browse products
        And I choose "Web-EU" as a channel filter
        And I filter
        Then I should see 2 products in the list
        And I should see a product with name "MacBook Air"
        And I should see a product with name "MacBook Pro"
        But I should not see any product with name "HP Spectre"

    @ui @api
    Scenario: Filtering products by a chosen channel
        When I browse products
        And I choose "Web-US" as a channel filter
        And I filter
        Then I should see a single product in the list
        And I should not see any product with name "MacBook Air"
        And I should not see any product with name "MacBook Pro"
        But I should see a product with name "HP Spectre"
