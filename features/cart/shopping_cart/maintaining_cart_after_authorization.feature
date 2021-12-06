@shopping_cart
Feature: Maintaining cart after authorization
    In order not to lose cart after authorization
    As a Visitor
    I want to be able to have my cart maintained

    Background:
        Given the store operates on a single channel in "United States"
        And on this channel account verification is not required
        And the store has a product "Stark T-Shirt" priced at "$12.00"
        And there is a user "robb@stark.com" identified by "KingInTheNorth"

    @ui @api
    Scenario: Having cart maintained after logging in
        When I add "Stark T-Shirt" product to the cart
        And I log in as "robb@stark.com" with "KingInTheNorth" password
        And I see the summary of my cart
        Then there should be one item in my cart
        And this item should have name "Stark T-Shirt"

    @ui @api
    Scenario: Having cart maintained after logging in when the user has been logged in earlier and has empty cart
        When I log in as "robb@stark.com" with "KingInTheNorth" password
        And I log out
        And I add "Stark T-Shirt" product to the cart
        And I log in as "robb@stark.com" with "KingInTheNorth" password
        And I see the summary of my cart
        Then there should be one item in my cart
        And this item should have name "Stark T-Shirt"

    @ui @api
    Scenario: Having cart maintained after logging in when the user has removed all items from the cart earlier
        When I log in as "robb@stark.com" with "KingInTheNorth" password
        And I add "Stark T-Shirt" product to the cart
        And I remove product "Stark T-Shirt" from the cart
        And I log out
        And I add "Stark T-Shirt" product to the cart
        And I log in as "robb@stark.com" with "KingInTheNorth" password
        And I see the summary of my cart
        Then there should be one item in my cart
        And this item should have name "Stark T-Shirt"

    @ui @api
    Scenario: Having cart maintained after logging in when the user has cleared the cart earlier
        When I log in as "robb@stark.com" with "KingInTheNorth" password
        And I add "Stark T-Shirt" product to the cart
        And I clear my cart
        And I log out
        And I add "Stark T-Shirt" product to the cart
        And I log in as "robb@stark.com" with "KingInTheNorth" password
        And I see the summary of my cart
        Then there should be one item in my cart
        And this item should have name "Stark T-Shirt"

    @ui @api
    Scenario: Having cart maintained after registration
        Given I have product "Stark T-Shirt" in the cart
        When I register with email "eddard@stak.com" and password "handOfTheKing"
        And I see the summary of my cart
        Then there should be one item in my cart
        And this item should have name "Stark T-Shirt"
