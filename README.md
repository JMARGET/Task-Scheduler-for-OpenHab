# Task-Scheduler-for-OpenHab
This PHP website works directly with OpenHab so that you can create time or sun based rules to automate some operations without writing any line OpenHab rules

If you want to have a better idea on what this is about, I recommend you take a look into my video that is also attached to the project.

The principle is as follow:
-A PHP Website that is running in parallel to OpenHab.
-This website uses OpenHab Rest API.
-This Website is used to configure some actions based on time (Day, Sunset, Sunrise).
-OpenHab should be configured to ping the website every 60s.
-If some actions are to be run, then the website contact OpenHab through the REST API and execute any scheduled task.
