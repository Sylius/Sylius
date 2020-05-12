@managing_products
Feature: Filtering products by a channel
    In order to filter products by a specific channel
    As an Administrator
    I want to be able to filter products on the list

    Background:
        Given the store operates on a channel named "Web-EU"
        And the store also operates on a channel named "Web-US"
        And the store has a product "Macbook-air" with channel "Web-EU"
        And the store also has a product "Macbook-pro" with channel "Web-EU"
        And the store also has a product "Hp" with channel "Web-US"
        And I am logged in as an administrator

    @ui
    Scenario: Filtering products by a chosen channel
        When I browse products
        And I choose "Web-EU" as a channel filter
        And I filter
        Then I should see 2 products in the list
        And I should see a product with name "Macbook-air"
        And I should see a product with name "Macbook-pro"
        But I should not see any product with name "Hp"

    @ui
    Scenario: Filtering products by a chosen channel
        When I browse products
        And I choose "Web-US" as a channel filter
        And I filter
        Then I should see a single product in the list
        And I should not see any product with name "Macbook-air"
        And I should not see any product with name "Macbook-pro"
        But I should see a product with name "Hp"
