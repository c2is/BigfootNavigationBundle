# Feat-6

This patch introduced changes in the data model - the name field in Menu\Item is being renamed label to homogeinize with other Bigfoot entities.

When upgrading to 2.2 from 2.1 or lower version, you will have to update your database schema and data.
To do so, you'll find in the Migrations/ directory a doctrine migration patch applying needed schema and data updates to upgrade your project to BigfootNavigationBundle 2.2.x needs and save your current data.

Copy the BigfootNavigationBundle:Migrations/Version2_2_6.php file into the app/DoctrineMigrations/ repertory in your project and rename as fit to have the migration patch execute - or use the SQL satetements inside as you see fit - to have your project migrate correctly with the data model changes.
