@shopping_cart
Feature: Maintaining previous cart after authorization
    In order to retrieve my previous unfinished order
    As a Visitor
    I want to be able to have my cart previous maintained

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Stark T-shirt" priced at "$12.00"
        And there is a user "robb@stark.com" identified by "KingInTheNorth"
        And I log in as "robb@stark.com" with "KingInTheNorth" password

    @ui
    Scenario: Having cart maintained after logging out and then logging in
        Given I have product "Stark T-Shirt" in the cart
        When I log out
        And I log in as "robb@stark.com" with "KingInTheNorth" password
        And I see the summary of my cart
        Then there should be one item in my cart
        And this item should have name "Stark T-shirt"
