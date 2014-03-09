Feature: Login
    In order to access to api
    I need to fetch / use a Kanbanize generated token

    Scenario: Successfull authentication with email and password
        Given I am unauthenticated user
        When I want to view tasks list
        Then I should insert boardid "2"
        And I should insert my email "name.surname@email.com"
        And I should insert my password "secretpassword"
        And I should view in the output "Please enter the board id:"
        And I should view in the output "Please insert your Kanbanize email:"
        And I should view in the output "Please insert your password:"
        And I should view in the output "The Task Title"
        And The client has no more input

    Scenario: Successfull authentication with email and password
        Given I am unauthenticated user
        When I want to view tasks list
        Then I should insert boardid "2"
        And I should insert my email "name.surname@email.com"
        And I should insert a wrong password "fakepassword"
        And I should insert my email again "name.surname@email.com"
        And I should insert my password again "secretpassword"
        And I should view in the output "Please enter the board id:"
        And I should view in the output "Please insert your Kanbanize email:"
        And I should view in the output "Please insert your password:"
        And I should view in the output "The Task Title"
        And The client has no more input
