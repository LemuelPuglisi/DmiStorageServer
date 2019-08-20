# DmiStorageServer
Departement of Mathematics and Informatics cloud based storage server.

### Contributing 

The code must be psr-1 psr-2 compliance; 

The branching strategy is Github Flow; 

The commits must complete the following sentence: "If I pull this commit, I will [ commit_name ]"; 

### Basic Idea

The server will store a minimum quantity of sensible data

The storage content will be public

Every user can request permissions to: 

- Upload in a folder

- Upload & delete in a folder

- Manage a whole folder

Admins will handle the requests.

Every admin has whole permissions on the file management system, so he can: 

- Accept requests by users
- Create / Delete a folder
- Create / Delete a course
- Upload / Remove a file

### Rest API

Small documentation about server's rest API.

##### Courses

>  [**GET**] localhost:8000/api/courses 

Return the entire list of courses and a 200 success code. 



>[**GET**] localhost:8000/api/sort/{param}/{order}

param must be choosed between year, id and cfu, order could be 'asc' or 'desc'.

It will throw an error if the params are wrong. Otherwise it will return the sorted list of courses.



> [**GET**] localhost:8000/api/courses/{course_id}

Return the course identified by the course_id and 200 success code if it exists.

Otherwise it will return NULL and a 404 Not found code. 



> [**POST**] localhost:8000/api/courses

**Parameters**: name, year, cfu

If the request is valid, it return a success message. The error field will be null. Will throw 200 success code. Else it return a failure message, an array of errors and a 400 code.



>[**PUT / PATCH**] localhost:8000/api/courses/{course_id}

**Parameters**: name, year, cfu (At least one of these)

If the request is valid, it return a success message and update the course identified by the course_id. The error field will be null and it will throw a 200 code. Else it return a failure message with his relative code.



> [**DELETE**] localhost:8000/api/courses/{course_id}

If the request is valid, it return a success message and delete the course identified by the course_id. The error field will be null and it will throw a 200 code. Else it return a failure message with his relative code.

