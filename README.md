# DMIStorageServer

Departement of Mathematics and Informatics cloud based storage server.

The [**API Documentation**](https://documenter.getpostman.com/view/8215228/SVfRv8rQ?version=latest) contains all the APIs this server offers.

*****

### Project Tree

- [**Requirements**](#Requirements)
- [**Installation**](#Installation)
- [**Contributing**](#Contributing)
- [**Structure**](#Structure)
- [**Infos**](#Infos)

*****

### Requirements

This is a Laravel project, so you can find the requirements on [Laravel Official Documentation - server requirements](https://laravel.com/docs/5.8#server-requirements) 

*****

### Installation 

Clone or download this repository: 

```sh
$ git clone https://github.com/LemuelPuglisi/DmiStorageServer.git
```

Run composer update:

```sh
$ composer update
```

Generate the key:

```sh
$ php artisan key:generate
```

Create a mySQL database: 

```mysql
CREATE DATABASE dmi_storage_db
```

Copy .env.example in .env and set your **db credentials** and your **smtp configurations**
```sh
$ cp .env.example .env
```

Run the migrations: 

```sh
$ php artisan migrate
```

Run initialize the passport clients
```sh
$ php artisan passport:install
```

Start a Laravel development server: 

```sh
$ php artisan serve
```

Add the following project cronjob to the crontab:

```sh
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Now you're ready to use our cloud!  

*Note: the root htaccess will fix a passport bug in apache2 servers*

*****

### Contributing 

Here's a list of rules that keep the work flow clean and the code maintainable.

**Branching Strategy:** [GitHub Flow](https://guides.github.com/introduction/flow/)

**Semantic Versioning**: [SemVer](https://semver.org/)

**Coding Standars:** [PSR-1](https://www.php-fig.org/psr/psr-1/)

**Coding Style:** [PSR-2](https://www.php-fig.org/psr/psr-2/)

*Notes: I strongly recommend to use [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to automatically make your code compliant with PSR-1 & PSR-2*

**Commit standard:** Our project commits have a title and a body. The title must complete correctly the following sentence: 

>  *If I pull this commit, I will ... [Commit title]*

Leave one line below the title and write a coincise body that shortly describe what did you do.

*****

### Structure

- Courses contains Folders
- Folders contains Files and Subfolders
- Folder and subfolders are in the same level, the database logic will manage the structure. 
- Courses, Folders and Files are readable without authentication.
- Users can choose to sign-up to contribute to the cloud management. 
- Every authenticated user can request global permissions to every course. 
- Every authenticated user can request both upload and remove permissions to every folder. 
- The System Administrator can promote users to admins.
- Admins have full access to everything.
- Admins will accept or deny the users' requests.



#### Security and Ethics

The only sensible data stored in our database are the email and the password.

The password will be hashed and salted. 

*****

### Infos

For more infos contact me by [email](emailto:lemuelpuglisi001@gmail.com).