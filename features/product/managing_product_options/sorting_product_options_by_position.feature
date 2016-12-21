@managing_product_options
Feature: Sorting listed product options by position
    In order to change the order by which product options are displayed
    As an Administrator
    I want to sort product options by their positions

    Background:
        Given the store has a product option "Mug size" at position 0
        And the store has also a product option "Mug color" at position 2
        And the store has also a product option "Mug type" at position 1
        And I am logged in as an administrator

    @ui
    Scenario: Product options are sorted by position in ascending order by default
        When I browse product options
        Then I should see 3 product options in the list
        And the first product option in the list should have name "Mug size"
        And the last product option in the list should have name "Mug color"

    @ui
    Scenario: Product option added at no position is added as the last one
        Given the store has a product option "Mug shape"
        When I browse product options
        Then the last product option in the list should have name "Mug shape"

    @ui
    Scenario: Payment method added at position 0 is added as the first one
        Given the store has a product option "Mug shape" at position 0
        When I browse product options
        Then the first product option in the list should have name "Mug shape"
