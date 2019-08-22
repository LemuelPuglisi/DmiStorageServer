# DMIStorageServer

Departement of Mathematics and Informatics cloud based storage server.

*****

### Project Tree

- [**Requirements**](#Requirements)
- [**Installation**](#Installation)
- [**Contributing**](#Contributing)
- [**Structure**](#Structure)
- [**API Documentation**](#Documentation)
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

Run the migrations: 

```sh
$ php artisan migrate
```

Start a Laravel development server: 

```sh
$ php artisan serve
```

Now you're ready to use our cloud! 

*****

### Contributing 

Here's a list of rules that keep the work flow clean and the code maintainable.

**Branching Strategy: ** [GitHub Flow](https://guides.github.com/introduction/flow/)

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
- Every authenticated user can request both upload and remove permissions to any of the course/folders. 
- The System Administrator can promote users to admins.
- Admins have full access to everything.
- Admins will accept or deny the users' requests.



#### Security and Ethics

The only sensible data stored in our database are the email and the password.

The password will be hashed and salted. 

The email will be encrypted. 

*****

### Documentation

To guarantee the correct basic usage of this server, here's a list of all REST APIs.

*Notes: front-end must rely to http response codes*



#### Course APIs



> **Name:** courses.index
>
> **Request:** localhost:8000/api/courses
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:**

```json
[
    {
        "id": 1,
        "name": "Algoritmi",
        "year": 2,
        "cfu": 9,
        "created_at": "2019-08-20 18:42:26",
        "updated_at": "2019-08-20 18:42:26"
    }
]
```



> **Name:** courses.show
>
> **Request:** localhost:8000/api/courses/{id}
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:**

```json
{
    "id": 1,
    "name": "Programmazione I",
    "year": 1,
    "cfu": 9,
    "created_at": "2019-08-20 18:42:26",
    "updated_at": "2019-08-20 18:42:26"
}
```



> **Name:** courses.store
>
> **Request:** localhost:8000/api/courses
>
> **Method:** POST
>
> **Params:** name, year, cfu
>
> **Response example:**

```json
Without errors
{
    "message": "Course created successfully",
    "errors": null
}

With errors
{
    "message": "Course not created successfully",
    "errors": {
        "cfu": [
            "The cfu field is required."
        ]
    }
}

```



> **Name:** courses.update
>
> **Request:** localhost:8000/api/courses/{id}
>
> **Method:** PUT|PATCH
>
> **Params:** name, year, cfu (Not all required)
>
> **Response example:**

```json
{
    "message": "Course updated successfully",
    "error": null
}
```



> **Name:** courses.destroy
>
> **Request:** localhost:8000/api/courses/{id}
>
> **Method:** DELETE
>
> **Params:** none
>
> **Response example:**

```json
{
    "message": "Course successfully deleted"
}
```



> **Name:** courses.sort
>
> **Request:** localhost:8000/api/courses/sort/{param}/{order}
>
> **Method:** GET
>
> **Params:** none
>
> **Notes**: {Param} must be choosed from {id, year, cfu, created_at, updated_at}, {order} could be 'asc' for ascendent or 'desc' for descendent. 
>
> **Response example:** Ordering by Id, descendent

```json
{
    "content": [
        {
            "id": 3,
            "name": "Algoritmi",
            "year": 2,
            "cfu": 9,
            "created_at": "2019-08-21 22:16:39",
            "updated_at": "2019-08-21 22:16:39"
        },
        {
            "id": 2,
            "name": "Programmazione II",
            "year": 1,
            "cfu": 9,
            "created_at": "2019-08-20 20:16:11",
            "updated_at": "2019-08-20 20:45:14"
        }
    ],
    "error": null
}
```



> **Name:** courses.folders
>
> **Request:** localhost:8000/api/courses/{id}/folders
>
> **Method:** GET
>
> **Params:** root
>
> **Notes:** If root = true, then it will display only root folders.
>
> **Response example:**

```json
{
    "content": [
        {
            "id": 4,
            "display_name": "Ereditarietà",
            "storage_name": "Ereditarietà",
            "influence": 0,
            "subfolder_of": null,
            "course_id": 1,
            "created_at": "2019-08-21 20:22:51",
            "updated_at": "2019-08-21 21:21:10"
        },
        {
            "id": 7,
            "display_name": "Template",
            "storage_name": "Template",
            "influence": 0,
            "subfolder_of": null,
            "course_id": 1,
            "created_at": "2019-08-21 21:07:57",
            "updated_at": "2019-08-21 21:07:57"
        }
    ],
    "error": null
}
```



> **Name:** courses.folders.sort 
>
> **Request:** localhost:8000/api/courses/{id}/folders/sort/{param}/{order}
>
> **Method:** GET
>
> **Params:** root
>
> **Notes**: {Param} must be choosed from {id, influence, created_at, updated_at}, {order} could be 'asc' for ascendent or 'desc' for descendent. If root = true, then it will display only root folders.
>
> **Response example:** Ordering by Id, descendent, root only folders

```json
{
    "content": [
        {
            "id": 7,
            "display_name": "Template",
            "storage_name": "Template",
            "influence": 0,
            "subfolder_of": null,
            "course_id": 1,
            "created_at": "2019-08-21 21:07:57",
            "updated_at": "2019-08-21 21:07:57"
        },
        {
            "id": 4,
            "display_name": "Ereditarietà",
            "storage_name": "Ereditarietà",
            "influence": 0,
            "subfolder_of": null,
            "course_id": 1,
            "created_at": "2019-08-21 20:22:51",
            "updated_at": "2019-08-21 21:21:10"
        }
    ],
    "error": null
}
```



> **Name:** courses.trend 
>
> **Request:** localhost:8000/api/courses/{id}/trend/{limit}
>
> **Method:** GET
>
> **Params:** none
>
> **Notes**: It will return the top {limit} files (by influence) from a course 
>
> **Response example:**

```json
{
    "content": [
        {
            "id": 3,
            "uid": "Limiti5d5e93bb90106",
            "name": "Limiti",
            "author": "Marco Rossi",
            "extension": "png",
            "influence": 1,
            "user_id": 1,
            "folder_id": 1,
            "created_at": "2019-08-22 13:08:11",
            "updated_at": "2019-08-22 18:54:01"
        },
        {
            "id": 5,
            "uid": "Teoremi5d5e9f943d2a1",
            "name": "Teoremi",
            "author": "Giuseppe Verdi",
            "extension": "jpg",
            "influence": 0,
            "user_id": 1,
            "folder_id": 1,
            "created_at": "2019-08-22 13:58:44",
            "updated_at": "2019-08-22 14:04:41"
        }
    ],
    "error": null
}
```



#### Folder APIs



> **Name:** folders.index
>
> **Request:** localhost:8000/api/folders
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:**

```json
[
    {
        "id": 4,
        "display_name": "Ereditarietà",
        "storage_name": "Ereditarietà",
        "influence": 0,
        "subfolder_of": null,
        "course_id": 1,
        "created_at": "2019-08-21 20:22:51",
        "updated_at": "2019-08-21 21:21:10"
    }
]
```



> **Name:** folders.show
>
> **Request:** localhost:8000/api/folders/{id}
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:**

```json
{
    "id": 4,
    "display_name": "Ereditarietà",
    "storage_name": "Ereditarietà",
    "influence": 0,
    "subfolder_of": null,
    "course_id": 1,
    "created_at": "2019-08-21 20:22:51",
    "updated_at": "2019-08-21 21:21:10"
}
```



> **Name:** folders.store
>
> **Request:** localhost:8000/api/folders
>
> **Method:** POST
>
> **Params:** display_name, course_id
>
> **Response example:**

```json
without errors
{
    "message": "Folder successfully created"
}
with errors
{
    "message": {
        "display_name": [
            "The display name has already been taken."
        ]
    }
}
```



> **Name:** folders.update
>
> **Request:** localhost:8000/api/folders/{id}
>
> **Method:** PUT|PATCH
>
> **Params:** display_name, course_id
>
> **Response example:**

```json
{
    "message": "Folder updated successfully"
}
```



> **Name:** folders.destroy
>
> **Request:** localhost:8000/api/folders/{id}
>
> **Method:** DELETE
>
> **Params:** none
>
> **Response example:**

```json
{
    "message": "Folder deleted successfully"
}
```



> **Name:** folders.course
>
> **Request:** localhost:8000/api/folders/{id}/course
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:**

```json
{
    "content": {
        "id": 1,
        "name": "Programmazione 1",
        "year": 1,
        "cfu": 9,
        "created_at": "2019-08-20 18:42:26",
        "updated_at": "2019-08-21 22:20:29"
    },
    "error": null
}
```



> **Name:** folders.parent
>
> **Request:** localhost:8000/api/folders/{id}/parent
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:**

```json
{
    "content": {
        "id": 7,
        "display_name": "Template",
        "storage_name": "Template",
        "influence": 0,
        "subfolder_of": null,
        "course_id": 1,
        "created_at": "2019-08-21 21:07:57",
        "updated_at": "2019-08-21 21:07:57"
    },
    "error": null
}
```



> **Name:** folders.subfolders
>
> **Request:** localhost:8000/api/folders/{id}/subfolders
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:**

```json
{
    "content": [
        {
            "id": 4,
            "display_name": "Ereditarietà",
            "storage_name": "Ereditarietà",
            "influence": 0,
            "subfolder_of": 7,
            "course_id": 1,
            "created_at": "2019-08-21 20:22:51",
            "updated_at": "2019-08-21 22:50:09"
        }
    ],
    "error": null
}
```



> **Name:** folders.files
>
> **Request:** localhost:8000/api/folders/{id}/files
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:** 

```json
{
    "content": [
        {
            "id": 3,
            "uid": "Limiti5d5e93bb90106",
            "name": "Limiti",
            "author": "Marco Rossi",
            "extension": "png",
            "influence": 1,
            "user_id": 1,
            "folder_id": 1,
            "created_at": "2019-08-22 13:08:11",
            "updated_at": "2019-08-22 18:54:01"
        },
        {
            "id": 5,
            "uid": "Teoremi5d5e9f943d2a1",
            "name": "Teoremi",
            "author": "Giuseppe Verdi",
            "extension": "jpg",
            "influence": 0,
            "user_id": 1,
            "folder_id": 1,
            "created_at": "2019-08-22 13:58:44",
            "updated_at": "2019-08-22 14:04:41"
        }
    ],
    "error": null
}
```





> **Name:** folders.files.extension
>
> **Request:** localhost:8000/api/folders/{id}/files/{ext}/ext
>
> **Method:** GET
>
> **Params:** none
>
> **Note:** This will return all the files with {ext} extension from the selected folder
>
> **Response example:** {png}

```json
{
    "content": [
        {
            "id": 3,
            "uid": "Limiti5d5e93bb90106",
            "name": "Limiti",
            "author": "Di Fazio",
            "extension": "png",
            "influence": 1,
            "user_id": 1,
            "folder_id": 1,
            "created_at": "2019-08-22 13:08:11",
            "updated_at": "2019-08-22 18:54:01"
        }
    ],
    "error": null
}
```



> **Name:** folders.files.sort
>
> **Request:** localhost:8000/api/folders/{id}/files/sort/{param}/{order}
>
> **Method:** GET
>
> **Params:** none
>
> **Note:** This will return all the files ordered by a {param} in an {order}
>
> **Response example:** {order by influence, descendant}

```json
{
    "content": [
        {
            "id": 3,
            "uid": "Limiti5d5e93bb90106",
            "name": "Limiti",
            "author": "Marco Rossi",
            "extension": "png",
            "influence": 1,
            "user_id": 1,
            "folder_id": 1,
            "created_at": "2019-08-22 13:08:11",
            "updated_at": "2019-08-22 18:54:01"
        },
        {
            "id": 5,
            "uid": "Teoremi5d5e9f943d2a1",
            "name": "Teoremi",
            "author": "Giuseppe Verdi",
            "extension": "jpg",
            "influence": 0,
            "user_id": 1,
            "folder_id": 1,
            "created_at": "2019-08-22 13:58:44",
            "updated_at": "2019-08-22 14:04:41"
        }
    ],
    "error": null
}
```



#### Files APIs



> **Name:** files.store 
>
> **Request:** localhost:8000/api/files
>
> **Method:** POST
>
> **Params:** name, author, folder_id, file (the file you want to store)
>
> **Response example:** 

```json
{
    "message": "File successfully uploaded"
}
```

 

> **Name:** files.index
>
> **Request:** localhost:8000/api/files
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:** 

```json
[
    {
        "id": 6,
        "uid": "integrali5d5f0b126f854",
        "name": "integrali",
        "author": "Marco Rossi",
        "extension": "jpeg",
        "influence": 0,
        "user_id": 1,
        "folder_id": 1,
        "created_at": "2019-08-22 21:37:22",
        "updated_at": "2019-08-22 21:37:22"
    }
]
```

> **Name:** files.show
>
> **Request:** localhost:8000/api/files/{id}
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:** 

```json
{
    "id": 6,
    "uid": "integrali5d5f0b126f854",
    "name": "integrali",
    "author": "Marco Rossi",
    "extension": "jpeg",
    "influence": 0,
    "user_id": 1,
    "folder_id": 1,
    "created_at": "2019-08-22 21:37:22",
    "updated_at": "2019-08-22 21:37:22"
}
```



> **Name:** files.update
>
> **Request:** localhost:8000/api/files/{id} 
>
> **Method:** PUT|PATCH 
>
> **Params:** name, author, folder_id (Not all required, but at least 1)
>
> **Response example:** 

```json
{
    "message": "File successfully updated"
}
```



> **Name:** files.destroy
>
> **Request:** localhost:8000/api/files/{id}
>
> **Method:** DELETE 
>
> **Params:** none
>
> **Response example:** 

```json
{
    "message": "File successfully deleted"
}
```



> **Name:** files.extension
>
> **Request:** localhost:8000/api/files/{ext}/ext 
>
> **Method:** GET
>
> **Params:** none
>
> **Note:** This will return all the files with {ext} extension
>
> **Response example:** {png}

```json
[
    {
        "id": 3,
        "uid": "Limiti5d5e93bb90106",
        "name": "Limiti",
        "author": "Marco Rossi",
        "extension": "png",
        "influence": 1,
        "user_id": 1,
        "folder_id": 1,
        "created_at": "2019-08-22 13:08:11",
        "updated_at": "2019-08-22 18:54:01"
    }
]
```

 

> **Name:** files.folder
>
> **Request:** localhost:8000/api/files/{id}/folder 
>
> **Method:** GET
>
> **Params:** none
>
> **Response example:**

```json
{
    "content": {
        "id": 1,
        "display_name": "Derivate",
        "storage_name": "Derivate",
        "influence": 0,
        "subfolder_of": null,
        "course_id": 2,
        "created_at": "2019-08-22 12:05:10",
        "updated_at": "2019-08-22 12:05:10"
    },
    "error": null
}
```

 

> **Name:** files.download
>
> **Request:** localhost:8000/api/files/{id}/download
>
> **Method:** GET
>
> **Params:** none
>
> **Response:** Forces the user's browser to download the file.



> **Name:** files.stream
>
> **Request:** localhost:8000/api/files/{id}/stream
>
> **Method:** GET
>
> **Params:** none
>
> **Response:**  Display a file directly in the user's browser instead of initiating a download.



### Infos

For more infos contact me by [email](lemuelpuglisi001@gmail.com).