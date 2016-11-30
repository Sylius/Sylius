@managing_users
Feature: Changing shop user's password
    In order to modify shop user credentials
    As an Administrator
    I want to be able to change shop user password

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "kibsoon@example.com" identified by "goodGuy"
        And I am logged in as an administrator

    @ui
    Scenario: Changing a password of a shop user
        When I change the password of user "kibsoon@example.com" to "veryGoodGuy"
        Then I should be notified that it has been successfully edited
        And I should be able to log in as "kibsoon@example.com" with "veryGoodGuy" password
