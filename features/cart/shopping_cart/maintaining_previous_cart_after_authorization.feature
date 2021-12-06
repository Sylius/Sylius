@shopping_cart
Feature: Maintaining previous cart after authorization
    In order to retrieve my previous unfinished order
    As a Visitor
    I want to be able to have my cart previous maintained

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Stark T-Shirt" and "Targaryen T-Shirt" products
        And there is a user "robb@stark.com" identified by "KingInTheNorth"
        And I log in as "robb@stark.com" with "KingInTheNorth" password

    @ui @api
    Scenario: Having cart maintained after logging out and then logging in
        When I add "Stark T-Shirt" product to the cart
        And I log out
        And I add "Targaryen T-Shirt" product to the cart
        And I log in as "robb@stark.com" with "KingInTheNorth" password
        And I see the summary of my previous cart
        Then there should be one item in my cart
        And this item should have name "Stark T-Shirt"
