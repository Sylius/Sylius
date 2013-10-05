@account
Feature: User account password change
    In order to enhance the security of my account
    As a logged user
    I want to be able to change password

    Background:
        Given I am logged in user
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
          And I should see "This value should be the user current password"

    Scenario: Changing my password with a wrong confirmation password
        Given I am on my account password page
         When I fill in "Current password" with "sylius"
          And I fill in "New password" with "newpassword"
          And I fill in "Confirmation" with "wrongnewpassword"
          And I press "Save changes"
         Then I should still be on my account password page
          And I should see "The entered passwords don't match"

    Scenario: Successfully changing my password
        Given I am on my account password page
         When I fill in "Current password" with "sylius"
          And I fill in "New password" with "newpassword"
          And I fill in "Confirmation" with "newpassword"
          And I press "Save changes"
         Then I should be on my account profile page
          And I should see "The password has been changed"

