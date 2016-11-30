@managing_shipping_methods
Feature: Sorting listed shipping methods by position
    In order to change the order by which shipping methods are displayed
    As an Administrator
    I want to sort shipping methods by their positions

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "Aardvark Stagecoach" at position 2
        And the store also allows shipping with "Narwhal Submarine" at position 0
        And the store also allows shipping with "Pug Blimp" at position 1
        And I am logged in as an administrator

    @ui
    Scenario: Shipping methods are sorted by position in ascending order by default
        When I am browsing shipping methods
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have name "Narwhal Submarine"
        And the last shipping method on the list should have name "Aardvark Stagecoach"

    @ui
    Scenario: Shipping method added at no position is added as the last one
        Given the store also allows shipping with "Yellow Submarine"
        When I want to browse shipping methods
        Then the last shipping method on the list should have name "Yellow Submarine"

    @ui
    Scenario: Shipping method added at position 0 is added as the first one
        Given the store also allows shipping with "Yellow Submarine" at position 0
        When I want to browse shipping methods
        Then the first shipping method on the list should have name "Yellow Submarine"
