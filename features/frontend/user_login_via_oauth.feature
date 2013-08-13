@users
Feature: Sign in to the store via OAuth
    In order to view my orders list
    As a visitor with an OAuth account
    I need to be able to log in to the store

    Background:
        Given I am not logged in
          And I am on the store homepage

    Scenario Outline: Get to the OAuth login page
         When I follow "Login"
          And I press the login with <provider_name> button
         Then I should be on the <provider_site> website
          And I should see the <provider_name> login form

        Examples:
          | provider_name | provider_site |
          | Amazon        | amazon.com    |
          | Google        | google.com    |

    Scenario Outline: Log in with username and password
        Given I follow "Login"
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
          | provider_name | provider_site | email_label                  | email_value               | password_label         | password_value                | button                          |
          | Amazon        | amazon.com    | What is your e-mail address? | a_valid_amazon_email_here | What is your password? | a_valid_amazon_password_here  | Sign in using our secure server |
          | Google        | google.com    | Email                        | a_valid_google_email_here | Password               | a_valid_google_password_here  | Sign in                         |

    Scenario Outline: Log in with bad credentials
        Given I follow "Login"
          And I press the login with <provider_name> button
         When I fill in the following:
            | <email_label>    | <email_value>    |
            | <password_label> | <password_value> |
          And I press "<button>"
         Then I should still be on the <provider_site> website
          And I should see "<error>"

        Examples:
          | provider_name | provider_site | email_label                  | email_value               | password_label         | password_value                  | button                          | error                                                                       |
          | Amazon        | amazon.com    | What is your e-mail address? | a_valid_amazon_email_here | What is your password? | an_invalid_amazon_password_here | Sign in using our secure server | There was an error with your E-Mail/Password combination. Please try again. |
          | Google        | google.com    | Email                        | a_valid_google_email_here | Password               | an_invalid_google_password_here | Sign in                         | The username or password you entered is incorrect.                          |

    Scenario Outline: Trying to login without credentials
        Given I follow "Login"
          And I press the login with <provider_name> button
         When I press "<button>"
         Then I should still be on the <provider_site> website
          And I should see "<error>"

        Examples:
          | provider_name | provider_site | button                          | error                                                 |
          | Amazon        | amazon.com    | Sign in using our secure server | Missing e-mail address. Please correct and try again. |
          | Google        | google.com    | Sign in                         | Enter your email address.                             |

    Scenario Outline: Trying to login as non existing user
        Given I follow "Login"
          And I press the login with <provider_name> button
         When I fill in the following:
            | <email_label>    | <email_value>    |
            | <password_label> | <password_value> |
          And I press "<button>"
         Then I should still be on the <provider_site> website
          And I should see "<error>"

        Examples:
          | provider_name | provider_site | email_label                  | email_value                          | password_label         | password_value               | button                          | error                                                                       |
          | Amazon        | amazon.com    | What is your e-mail address? | an_invalid_amazon_email_address_here | What is your password? | a_valid_amazon_password_here | Sign in using our secure server | There was an error with your E-Mail/Password combination. Please try again. |
          | Google        | google.com    | Email                        | an_invalid_google_email_address_here | Password               | a_valid_google_password_here | Sign in                         | The username or password you entered is incorrect.                          |
