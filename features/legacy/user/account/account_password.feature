@legacy @account
Feature: User account password change
    In order to enhance the security of my account
    As a logged user
    I want to be able to change password

    Background:
        Given store has default configuration
        And I am logged in user
        And I am on my account homepage

    Scenario: Viewing my password change page
        Given I follow "My password"
        Then I should be on my account password page

    Scenario: Changing my password with a wrong current password
        Given I am on my account password page
        When I fill in "Current password" with "wrongpassword"
        And I fill in "New password" with "newpassword"
        And I fill in "Confirmation" with "newpassword"
        And I press "Save changes"
        Then I should still be on my account password page
        And I should see "Provided password is different than the current one"

    Scenario: Changing my password with a wrong confirmation password
        Given I am on my account password page
        When I fill in "Current password" with "sylius"
        And I fill in "New password" with "newpassword"
        And I fill in "Confirmation" with "wrongnewpassword"
        And I press "Save changes"
        Then I should still be on my account password page
        And I should see "The entered passwords don't match"

    Scenario: Successfully changing my password
        When I change my account password to "newpassword"
        Then I should be on my account homepage
        And I should see "Your password has been changed successfully!"

    Scenario: Logging in after password change
        Given I have changed my account password to "newpassword"
        When I click "Logout"
        And I log in with "sylius@example.com" and "newpassword"
        Then I should be redirected to the store homepage
        And I should see "Logout"
