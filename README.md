# DMIStorageServer

Departement of Mathematics and Informatics cloud based storage server.

*****

### Project Tree

- [**Requirements**](#Requirements)

- [**Contributing**](#Contributing)
- [**Structure**](#Structure)
- [**API Documentation**](#Documentation)
- [**Infos**](#Infos)

*****

### Requirements

This is a Laravel project, so you can find the requirements on [Laravel Official Documentation - server requirements](https://laravel.com/docs/5.8#server-requirements) 

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
> **Notes**: {Param} must be choosed from {id, year, cfu}, {order} could be 'asc' for ascendent or 'desc' for descendent. 
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
> **Notes**: {Param} must be choosed from {id, influence}, {order} could be 'asc' for ascendent or 'desc' for descendent. If root = true, then it will display only root folders.
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



### Infos

For more infos contact me by [email](lemuelpuglisi001@gmail.com).