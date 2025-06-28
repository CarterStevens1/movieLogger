# TODO

## Users

    ### Can log in (X - DONE)
        - Can log in with a username and password (X - DONE)
        - Error if incorrecct username or password (X - DONE)
    ### Can log out (X - DONE)
    ### Can create a new user (X - DONE)
        - Error if email already exists
    ### Can edit a user (X - DONE)
        - Error if email already exists
        - Error if using invalid character

## Tasks

    ### Can create a new board (X - DONE)
        - Can create a new movie with a board (X - DONE)
        - Can add tags to a board (X - DONE)

    ### Can edit a board (X - DONE)

    ### Can delete a board (X - DONE)

    ### Can Create a board (X - DONE)
        - Can delete a board (X - DONE)
        - Can give a board a title/name (X - DONE)

    ### Can Assign "boards" to a user
        - Can assign a board to a user (X - DONE)
        - Can delete a board (X - DONE)
        - Can assign a board to multiple users (X - DONE)
          -- New joining table between users and boards - Get board ID and all the shared user ID's (X - DONE)
          --- 3 columns (id_user_id, board_id, shared_user_id) (X - DONE)
          1_1_1_2
          1_1_1_3
          --- Add constrain if board is already shared with the user (X - DONE)
          (Model?, Migration) (Check documentation for how to create joiner table)
        - Can unassign a board from multiple users
          -- Delete row inside the new joining table

    ### Can make a board public or private

  ----------- 28/06/2025 ----------------

    ### Can create username with account (X - DONE)
      - Create a new user with a username and password and email (X - DONE)
      - Error if username already exists (X - DONE)
      - Error if email already exists (X - DONE)
      - Error if using invalid character
      - Error if password is less than 8 characters (X - DONE)

    ### Can give privelages to shared user (admin, editer, viewer)
      - Update migration to add new column to shared board table of "privelages"
      -- Admin can edit table, import and export and share with other users
      -- Editor can edit table
      -- Viewer can view table

    ### Add pre-confirm to delete board
      - Add a confirm dialog before deleting a board
    
    ### Add pre-confirm to delete user
      - Add a confirm dialog before deleting a user
    
    ### Add list of users shared with a board
      - Add a list of users shared with a board
      - Add ability to change privelages of a user
      - Add a delete button to remove the user from the board
        - Add a confirm dialog before deleting a user

    ### Change home page on login to dashboard (X - DONE)

    ### Update import to ask for comfirmation before importing

    ### Add history table to store all changes to allow roll back


## BUGS

### Tags dont follow when you sort column
### Import CSV still crashes sometimes on large files


<!--
Create a table that can add rows and columns
    - Add ability to add a top level column which can have any label (e.g A - B - C)
    - Add ability to add a row to the whole column which can have any label
      - For each column add equal amount of spanning row boxes
    - Allow row to be deleted (with confirmation if there are columns in it)
      - If row is deleted, delete all from the full row (Horizontal association)
    - Allow column to be deleted (with confirmation if there are rows in it)
      - If column is deleted, delete all associated rows (Vertical association)
    - Sort rows vertically by A-Z
    - Allow columns to be draggable to readjust the size and retain that new size on refresh
    - Add ability to create tags (TBD)
      - Create them on board creation and allow users to select them during movie add
    -For each row or column added save and retain aswell as any content added save and retain.
-->

<!-- 
A table that can have rows and columns
I need a Column database table - Has many cells
a Row database table - Has many cells
a Cell database table - belongs to column and row
