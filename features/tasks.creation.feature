Feature: Creation
    In order to created new task
    I need to ...

    Scenario: Created new Tasks
        Given I am an authenticated user
        When I want to view tasks list
        Then I should not view in the output "task created for testing purpose"
        When I want to create a new tasks
        And I use the option "--board 2"
        And I insert the title "task created for testing purpose"
        Then I should view in the output "id is: `.*`"
        When I want to view tasks list
        And I'm waiting task creation
        And I use the option "--project 1"
        And I use the option "--board 2"
        And I use the option "--no-cache"
        Then I should view in the output "task created for testing purpose"

