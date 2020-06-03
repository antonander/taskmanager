Task manager
============
An easy to use task manager that allows you to keep track of the amount of time you spend on every task during the day.

Note: If you want to use the custom php file included to manage your tasks, just rememeber that all times are under the Coordinated Universal Time (UTC).

[**Demo**](http://gaming4learning.com/taskmanagerdemo/)

## Installation

### 1. Clone this Github repository

Find a location on your computer where you want to store the project and open the command line there. Next, use the following command which will pull the project from github and create a copy of it on your local computer at the sites directory inside another folder called “projectName”.

```
git clone https://github.com/antonander/taskmanager.git
```

### 2. Cd into your project

You will need to be inside that project file to enter all of the rest of the commands. So remember to type cd projectName to move your terminal working location to the project file we just barely created. (Of course substitute “projectName” in the command above, with the name of the folder you created in the previous step).

### 2. Enter into your project's folder

You will need to be inside that project file to enter all of the rest of the commands. So remember to type cd projectName to move your terminal working location to the project file we just barely created. (Of course substitute “projectName” in the command above, with the name of the folder you created in the previous step).

```
cd project name
```

### 3. Install Composer Dependencies

Since this project was created with Laravel, you must now install all of the project dependencies.

When we run composer, it checks the composer.json file, which is a file that lists all of the dependencies the project needs to run. Because these packages are constantly changing, the source code is generally not submitted to github, but instead we let composer handle these updates. So to install all this source code we run composer with the following command.

```
composer install
```

### 4. Install NPM dependencies

Pretty much what we did on the last step. In this case, NPM takes care of the installation of Vue.js, Bootstrap.css, amont others.

```
npm install
```

### 5. Create a copy of your .env file

.env files store sensitive information such as credentials to access a database. For this reason, they are not generally committed to source control for security reasons. But there is a .env.example which is a template of the .env file that the project expects us to have. So all you have to do is make a copy of the .env.example file and create a .env file that we can start to fill out to do things like database configuration.

```
cp .env.example .env
```
Note: Remember that, if for any reason you can't create the copy with the command given, you can always do it manually duplicating the file inside the project folder.

### 6. Create a copy of your .env file

Laravel requires you to have an app encryption key which is generally randomly generated and stored in your .env file. The app will use this encryption key to encode various elements of your application from cookies to password hashes and more. To generate your encryption key, simply use this command:

```
php artisan key:generate
```

### 6. Create an empty database for your application

Create an empty database with the name "taskmanager". To do this, you can either use the command line or a program such as [MAMP](https://www.mamp.info/en/downloads/) or [XAMPP](https://www.apachefriends.org/es/download.html).

If you want to use the command line, just remember that you have to install MySQL before anything else. However, if you go with MAMP or XAMPP, you won't have to worry. MySQL comes included.

If you need further assistance on how to create the database, this article can be very useful: https://www.instructables.com/id/Creating-a-Database-With-XAMPP/

### 6. In the .env file, add database information to allow Laravel to connect to the database

We will want to allow Laravel to connect to the database that you just created in the previous step. To do this, we must add the connection credentials in the .env file and Laravel will handle the connection from there.

In the .env file fill in the DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD options to match the credentials of the database you just created. This will allow us to run migrations and seed the database in the next step.

Note: Normally, is you are on a localhost, these are the credentials you will have to use:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskmanager
DB_USERNAME=root
DB_PASSWORD=root

### 7. Migrate the database

Once your credentials are in the .env file, now you can migrate your database.

```
php artisan migrate
```

It’s not a bad idea to check your database to make sure everything migrated the way you expected.

### 8. Run the application

That's it, now you should be able to run the application using this command:

```
php artisan serve
```

## Using the custom PHP file.

To use the custom PHP file all you have to do is get from the phpscript folder inside this project (if you cloned this repository you should have it already).

As you can see, this folder comes with two files, the script (named "taskmanager.php"), and a script to create the database. You will only need this last one to import the needed information into the database in case you haven't done it already following the installation process.

With this script, you can run three types of actions: start, stop and summary.

Example of how to start a new task:

```
php taskmanager.php start web development
```

Example of how to stop an active task:

```
php taskmanager.php stop web development
```

Example of how to show the summary:

```
php taskmanager.php summary
```

Important: Before you start playing with the script, just remember that you have to have MySQL running for it to work. You can do this with MAMP or XAMPP. Also, if you have different credentials for the database, feel free to change them inside the file.

## Useful notes

### About this project

This project, essentially tracks time in two different ways. The first one, (which is the one employed with the Laravel version), is via easytimer.js a js library. The easytimer.js allowed me to simply take the current time (after the user started the timer) and send it to the model.

However for the script version, since this was pure PHP I decided to track time with dates. This means that I store the time when the user hits the "start" button, and compare it to the time when they hit the "stop" button.

Both process work together and are connected to the same database.

### Where is your code?

If you have no prior experience with Laravel, it might be hard to locate the code I created, so, to save you some time:

- public/css/main.min.css -> The CSS used (minified).
- public/js/main.min.js -> The JS libraries used (minified).
- resources/views/taskmanager.blade.php -> The view I created to get the information and send it to the controller.
- app/database/migrations -> The set up of the database.
- app/Http/Controllers/TaskManagerController.php -> The controller for the data.
- app/TaskManager.php -> The model.
