@managing_promotions
Feature: Seeing correct percentage discounts while editing promotion with action and decimal places
    In order to see the accurate percentage amount while editing the promotion
    As a store owner
    I want to see the correct percentage amount while editing the promotion with decimal places

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Cheap Stuff"
        And this promotion gives "12.00%" discount to every order
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing an accurate percentage amount after editing the promotion including the value up to one decimal place
        When I want to modify a "Cheap Stuff" promotion
        And I edit this promotion percentage action to have "2.5%"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should have "2.50%" of order percentage discount

    @api @ui
    Scenario: Seeing an accurate percentage amount after editing the promotion including the value up to two decimal places
        When I want to modify a "Cheap Stuff" promotion
        And I edit this promotion percentage action to have "2.56%"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should have "2.56%" of order percentage discount

    @ui @no-api
    Scenario: Seeing an accurate percentage amount after using a comma as a decimal separator
        When I want to modify a "Cheap Stuff" promotion
        And I edit this promotion percentage action to have "2,56%"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should have "2.56%" of order percentage discount
