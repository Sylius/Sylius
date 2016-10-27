@managing_users
Feature: Changing shop user password
    In order to modify shop user credentials
    As an Administrator
    I want to be able to change shop user password

    Background:
        Given there is a user "kibsoon@example.com" identified by "goodGuy"
        And I am logged in as an administrator

    @ui @todo
    Scenario: Changing password of shop user
        When I change user "kibsoon@example.com" password to "veryGoodGuy"
        Then I should be notified that it has been successfully edited
        And I should be able to log in as "kibsoon@example.com" with "veryGoodGuy" password
