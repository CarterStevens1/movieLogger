# TODO

## Users

    ### Can log in (X - DONE)
        - Can log in with a username and password (X - DONE)
        - Error if incorrecct username or password (X - DONE)
    ### Can log out (X - DONE)
    ### Can create a new user (X - DONE)
        - Error if username already exists
    ### Can edit a user (X - DONE)
        - Error if username already exists
        - Error if using invalid character

## Tasks

    ### Can create a new movie
        - Can create a new movie with a title
        - Can add tags to a movie

    ### Can edit a movie

    ### Can delete a movie

    ### Can Create a board (X - DONE)
        - Can delete a board (X - DONE)
        - Can give a board a title/name (X - DONE)

    ### Can Assign "boards" to a user
        - Can assign a board to a user (X - DONE)
        - Can delete a board (X - DONE)
        - Can assign a board to multiple users (X - DONE)
          -- New joining table between users and boards - Get board ID and all the shared user ID's
          --- 3 columns (id_user_id, board_id, shared_user_id)
          1_1_1_2
          1_1_1_3
          --- Add constrain if board is already shared with the user
          (Model?, Migration) (Check documentation for how to create joiner table)
        - Can unassign a board from multiple users
          -- Delete row inside the new joining table

    ### Can make a board public or private

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
