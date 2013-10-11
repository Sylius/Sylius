@users @oauth
Feature: User registration via OAuth
    In order to order products
    As a visitor with an OAuth account
    I need to be able to create an account in the store

    Background:
        Given I am not logged in
          And I am on the store homepage

    Scenario Outline: Get to the OAuth login page
         When I follow "Register"
          And I press the login with <provider_name> button
         Then I should be on the <provider_site> website
          And I should see the <provider_name> login form

        Examples:
          | provider_name | provider_site |
          | Amazon        | amazon.com    |
          | Facebook      | facebook.com  |
          | Google        | google.com    |

    Scenario Outline: Successfully creating account in store
        Given I follow "Register"
          And I press the login with <provider_name> button
         When I fill in the following:
           | <email_label>    | <email_value>    |
           | <password_label> | <password_value> |
          And I press "<button>"
          And I allow the use of my <provider_name> account (if I am still on the <provider_site> website)
         Then I should not be on the <provider_site> website anymore
          But I should be on the store homepage
          And I should see "Logout"

        Examples:
          | provider_name | provider_site | email_label                  | email_value               | password_label         | password_value               | button                          |
          | Amazon        | amazon.com    | What is your e-mail address? | a_valid_amazon_email_here | What is your password? | a_valid_amazon_password_here | Sign in using our secure server |
          | Google        | google.com    | Email                        | a_valid_google_email_here | Password               | a_valid_google_password_here | Sign in                         |

    Scenario Outline: Register with already existing email
        Given there are following users:
            | email   |
            | <email_value> |
          And I follow "Register"
          And I press the login with <provider_name> button
         When I fill in the following:
            | <email_label>    | <email_value>    |
            | <password_label> | <password_value> |
          And I press "<button>"
         Then I should not be on the <provider_site> website anymore
          But I should be on the store homepage
          And I should see "Logout"

        Examples:
          | provider_name | provider_site | email_label                  | email_value                       | password_label         | password_value               | button                          |
          | Amazon        | amazon.com    | What is your e-mail address? | a_valid_amazon_email_address_here | What is your password? | a_valid_amazon_password_here | Sign in using our secure server |
          | Google        | google.com    | Email                        | a_valid_google_email_address_here | Password               | a_valid_google_password_here | Sign in                         |
