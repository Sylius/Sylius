Feature: User account password change
    In order to enhance the security of my account
    As a logged user
    I want to be able to change password

    Background:
        Given I am logged in user
          And I am on my account homepage

    Scenario: Viewing my password change page
        Given I follow "My password"
         Then I should be on the account password page

    Scenario: Changing my password with a wrong current password
        Given I am on the account password page
         When I fill in "CurrentPassword" with "wrongpassword"
          And I fill in "Password" with "newpassword"
          And I fill in "ConfirmationPassword" with "newpassword"
          And I press "Submit"
         Then I should still be on the account password page
          And I should see "The password you provided does not match the account you are logged in with"

    Scenario: Changing my password with a confirmation password
        Given I am on the account password page
         When I fill in "CurrentPassword" with "password"
          And I fill in "Password" with "newpassword"
          And I fill in "ConfirmationPassword" with "wrongnewpassword"
          And I press "Submit"
         Then I should still be on the account password page
          And I should see "The new password does not match the confirmation"

    Scenario: Successfully changing my password
        Given I am on the account password page
         When I fill in "CurrentPassword" with "password"
          And I fill in "Password" with "newpassword"
          And I fill in "ConfirmationPassword" with "newpassword"
          And I press "Submit"
         Then I should still be on the account password page
          And I should see "Your password has been changed"

