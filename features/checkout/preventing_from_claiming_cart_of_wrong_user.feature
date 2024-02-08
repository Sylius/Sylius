@checkout
Feature: Preventing from claiming cart of a wrong user
    In order to make the checkout cart available only for user who owns the cart
    As a Customer
    I want to be able to checkout with my previous cart when someone used my email in checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$20.00"
        And the store has a product "Kotlin T-Shirt" priced at "$30.00"
        And the store has a product "Symfony T-Shirt" priced at "$100.00"
        And the store has a product "Sylius T-Shirt" priced at "$150.00"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a user "robb@stark.com" identified by "KingInTheNorth"

    @ui @mink:chromedriver @no-api
    Scenario: Preventing anonymous user from claiming cart of logged in user
        Given I am logged in as "robb@stark.com"
        And I have product "PHP T-Shirt" in the cart
        When an anonymous user in another browser adds products "PHP T-Shirt" and "Kotlin T-Shirt" to the cart
        And they complete addressing step with email "robb@stark.com" and "United States" based billing address
        And they add product "Symfony T-Shirt" to the cart
        Then their cart total should be "$150.00"

    @ui @mink:chromedriver @no-api
    Scenario: Preventing anonymous user from claiming cart of logged in user
        Given I am logged in as "robb@stark.com"
        And I have product "PHP T-Shirt" in the cart
        When an anonymous user in another browser adds products "PHP T-Shirt" and "Kotlin T-Shirt" to the cart
        And they complete addressing step with email "robb@stark.com" and "United States" based billing address
        And they add product "Symfony T-Shirt" to the cart
        And I view my cart in the previous session
        Then there should be one item in my cart
        And my cart's total should be "$20.00"

    @ui @no-api
    Scenario: Preventing anonymous user from claiming cart of logged in user
        Given I have product "PHP T-Shirt" in the cart
        When I sign in with email "robb@stark.com" and password "KingInTheNorth"
        And I log out
        And an anonymous user in another browser adds products "PHP T-Shirt" and "Kotlin T-Shirt" to the cart
        And they complete addressing step with email "robb@stark.com" and "United States" based billing address
        And they add product "Symfony T-Shirt" to the cart
        And I sign in again with email "robb@stark.com" and password "KingInTheNorth" in the previous session
        And I see the summary of my previous cart
        Then there should be one item in my cart
        And my cart's total should be "$20.00"

    @ui @no-api
    Scenario: Preventing anonymous user from claiming cart of logged in user
        Given on this channel account verification is not required
        And I have product "PHP T-Shirt" in the cart
        When I register with email "eddard@stark.com" and password "handOfTheKing"
        And I log out
        And an anonymous user in another browser adds products "PHP T-Shirt" and "Kotlin T-Shirt" to the cart
        And they complete addressing step with email "robb@stark.com" and "United States" based billing address
        And they add product "Symfony T-Shirt" to the cart
        And I sign in again with email "eddard@stark.com" and password "handOfTheKing" in the previous session
        And I see the summary of my previous cart
        Then there should be one item in my cart
        And my cart's total should be "$20.00"

    @ui @no-api
    Scenario: Preventing logged in user from claiming cart of anonymous user
        Given an anonymous user added product "Kotlin T-Shirt" to the cart
        And they have completed addressing step with email "robb@stark.com" and "United States" based billing address
        When I log in as "robb@stark.com"
        And I add product "Sylius T-Shirt" to the cart
        And I view my cart in the previous session
        Then there should be one item in my cart
        And my cart's total should be "$150.00"
