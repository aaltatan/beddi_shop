# BEDDI SHOP ONLINE STORE

```
MCS WP2 HOMEWORK REPORT FOR PhD. Bassel Alkhateeb
Abdullah Altatan
abdullah_232943
C1
```

- [`Folder Structure`](#folder-structure)
- [`Database Structure`](#database-structure)
- [`Application Logic`](#application-logic)

## FOLDER STRUCTURE:

i divide my project into **two** main sections, **admin dashboard** and **main site** like below:

```
C:\xampp\htdocs\beddi_shop
+---- admin
| +---- includes
| | +---- functions
| | \---- templates
| \---- layout
| +---- css
| +---- images
| +---- js
| \---- scss
| \---- pages
|
+---- api
|
+---- data
| \---- uploads
|
+---- includes
| \---- templates
|
\---- layout
+---- css
+---- fontawesome
+---- images
+---- js
\---- scss
\---- pages
```

each one has its own style and functionality using SASS and JavaScript.

## DATABASE STRUCTURE:

- [`users`](#users-table)
- [`categories`](#categories-table)
- [`items`](#items-table)
- [`comments`](#comments-table)
- [`cart`](#cart-table)
- [`items_images`](#items-images-table)
- [`items_likes`](#items-likes-table)
- [`first_run`](#first-run-table)

### USERS TABLE:

```
+-------+--------+--------+-------+---------+--------+--------------+----------+--------+
|user_id|username|password| email |full_name|group_id|trusted_status|reg_status|   dt   |
+-------+--------+--------+-------+---------+--------+--------------+----------+--------+
|INTEGER| VARCHAR| VARCHAR|VARCHAR| VARCHAR |   INT  |      INT     |    INT   |DATETIME|
+-------+--------+--------+-------+---------+--------+--------------+----------+--------+
```

1. user_id : user id which will be used in each relation (auto increment)
2. username : which will be used to log in
3. password : will be stored as hashed password using sha1 function
4. email : user email address
5. full name: which will be used to present user_id itself
6. group_id : 1 for admin, 0 for regular users
7. trusted_status : 1 for trusted users , 0 for regular users
8. reg_status: registration status , 1 from accepted users by admin , 0 for non accepted ones.
9. dt: add date , default NOW() function.

### CATEGORIES TABLE:

```
+---+--------+--------+--------+----------+-------------+---------+
| id|cat_name|cat_desc|ordering|visibility|allow_comment|allow_ads|
+---+--------+--------+--------+----------+-------------+---------+
|INT| VARCHAR|  TEXT  |   INT  |  TINYINT |   TINYINT   | TINYINT |
+---+--------+--------+--------+----------+-------------+---------+
```

1. id : category id which will be used in each relation (auto increment)
2. cat_name : which will be used to present category id itself
3. cat_desc : category description
4. ordering : in case the admin need to sort the categories on his own.
5. visibility : 1 to enable visibility of the category, 0 for hide it.
6. allow_comment : 1 to enable comment in each item in this category , 0 for not.
7. allow_ads : 1 to enable advertisements in each item in this category , 0 for not.

### ITEMS TABLE:

```
+-------+---------+---------+----------+-----------+--------+------------+------+-------+---------+----------+----------+--------+
|item_id|item_name|item_desc|item_price|offer_price|add_date|country_made|cat_id|user_id|available|acceptable|is_special|is_cover|
+-------+---------+---------+----------+-----------+--------+------------+------+-------+---------+----------+----------+--------+
|  INT  | VARCHAR |   TEXT  |    INT   |    INT    |DATETIME|   VARCHAR  |  INT |  INT  | TINYINT |  TINYINT |  TINYINT | TINYINT|
+-------+---------+---------+----------+-----------+--------+------------+------+-------+---------+----------+----------+--------+
```

1. item_id = item id which will be used in each relation (auto increment)
2. item_name : which will be used to present item id itself
3. item_desc : item description
4. item_price : item price
5. offer_price : item offer price if exists
6. add_date : add date , default NOW() function.
7. country_made : made country for item
8. cat_id : category id (FOREIGN KEY for id in categories)
9. user_id : user id (FOREIGN KEY for user_id in users)
10. available : 1 if it is available to show in site , 0 for not
11. acceptable : 1 if it is acceptable by admin to show in site , 0 for not
12. is_special : 1 if it is added to special section on main site , 0 for not
13. is_cover : 1 if it is the landing photo on main site , 0 for not

### COMMENTS TABLE:

```
+----------+-------+--------------+----------+-------+-------+
|comment_id|comment|comment_status|added_date|item_id|user_id|
+----------+-------+--------------+----------+-------+-------+
|    INT   |  TEXT |    TINYINT   | DATETIME |  INT  |  INT  |
+----------+-------+--------------+----------+-------+-------+
```

1. comment_id : comment id which will be used in each relation (auto increment)
2. comment : comment content.
3. comment_status : 1 if it is acceptable by admin to show in site , 0 for not.
4. added_date : add date , default NOW() function.
5. item_id : item id (FOREIGN KEY for id in items)
6. user_id : user id (FOREIGN KEY for user_id in users)

### CART TABLE:

```
+-------+-------+--------+--------+
|user_id|item_id|quantity|add_date|
+-------+-------+--------+--------+
|  INT  |  INT  |   INT  |DATETIME|
+-------+-------+--------+--------+
```

1. user_id : user id (FOREIGN KEY for user_id in users)
2. item_id : item id (FOREIGN KEY for item_id in items)
3. cat_id : category id (FOREIGN KEY for id in categories)
4. add_date : add date , default NOW() function.

### ITEMS IMAGES TABLE:

```
+-------+----+
|item_id| img|
+-------+----+
|  INT  |TEXT|
+-------+----+
```

1. item_id : item id (FOREIGN KEY for item_id in items) | the same item_id auto_increment from **information_schema.TABLES**
2. img : image relative path

### ITEMS LIKES TABLE:

```
+-------+-------+
|item_id|user_id|
+-------+-------+
|  INT  |  INT  |
+-------+-------+
```

1. item_id : item id (FOREIGN KEY for item_id in items)
2. user_id : user id (FOREIGN KEY for user_id in users)

### FIRST RUN TABLE

```
+-------+
|has_ran|
+-------+
|TINYINT|
+-------+
```

1. has_ran : it's like a variable hold 1 if the script run for the first time, 0 if not.

## APPLICATION LOGIC:

- [`Admin Dashboard`](#admin-dashboard)
- [`Main Site`](#main-site)

### ADMIN DASHBOARD:

it includes five logic sections:

1. [`Admin Login Script`](#admin-login-script)
2. [`Connect Script`](#connect-script)
3. [`Initialize Script`](#initialize-script)
4. [`Admin Dashboard`](#admin-dashboard)
5. [`Admin Logout Script`](#admin-logout-script)

#### Admin Login Script:

it has php script which has an admin login form like :

```
<div class="login-bg">
    <form class="login" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
        <h1>Admin Login</h1>
        <input class="login-input" type="text" name="user" placeholder="enter your username" autocomplete="off">
        <input class="login-input" type="password" name="pass" placeholder="enter your password" autocomplete="new-password">
        <input type="submit" class="btn btn-primary" value="Log in">
    </form>
</div>
```

this script is responsible for checking if the user name is exists and password matching and it will create a session if the conditions were applied

#### Connect Script:

it has php script which has a PDO object to make a connection with MySQL database  
this script is responsible for creating all tables if not exists and inserting some experimental data to make life easier for testing

#### Initialize Script:

it has php script which it is like a holder for each page needing **(connect.php,regular expression for form validation,routes, header.php path adn functions.php path)** like below:

```
<?php

include "connect.php";

// Regular Expression Patterns:

$username_re = '/^[a-z]+[a-z0-9\.]{4,20}$/';
$email_re = '/^[A-Za-z0-9\.\-_]+@[a-z0-9\-]+\.\w{2,4}$/';
$password_re = '/^.{8,}$/';
$full_name_re = '/^[A-Z][A-Za-z\s]{2,48}[a-z]$/';
$name_country_re = '/^[A-Z][a-z]{3,19}$/';
$description_title_re = '/^.{4,50}$/';
$price_order_re = '/^\d+$/';

// routes:

$tpl = "./includes/templates/";
$func = "./includes/functions/";

// Includes:

include $func . "functions.php";
include $tpl . "header.php";

```

this php file will be included in first of each page like **dashboard.php**, **categories.php** ....

#### Admin Dashboard:

it includes:

- dashboard.php : which has a simple statistics about likes,images,pending items,pending users and last records from comments and items images, and also includes a bunch of links to make the life of admin easier to access all data from one section, it also have a dark\light switch and search box for easiest access to data.

and it includes four php scripts:

1. categories.php
2. items.php
3. users.php
4. comments.php

which each one has been divided into sections in the same script for basic crud operations using GET request by declaring $do variable and make a switch statement for each web query that come to the script from GET method like this:

```
<?php

ob_start();

session_start();

if (isset($_SESSION["admin"])) {

    $page_title = 'Members';
    include "init.php";
    include $tpl . "aside.php";

    $do = isset($_GET['do']) ? $_GET['do'] : "Manage";

    switch ($do) {
        case "Manage":
            // some content
            break;
        case "Pending":
            // some content
            break;
        case "Delete":
            // some content
            break;
        case "Activate":
            // some content
            break;
        case "Deactivate":
            // some content
            break;
        case "Insert":
            // some content
            break;
        case "Add":
            // some content
            break;
        case "Edit":
            // some content
            break;
        case "Update":
            // some content
            break;
        default:
            $msg = "there is no page like $do";
            redirect($msg, "members.php", 1, "danger");
    }

    include $tpl . "footer.php";

} else {
    header("Location: index.php");
    exit();
}

ob_end_flush();

```

each script will check for a session existing by "admin" name, if it's not , the script will redirect the user to index.php which is login script to prevent illegal accessing for the page, and each script will includes **init.php** and **aside.php** and **footer.php** at the end.

- Manage Section : contains the table to show info about the table of its kind like (users,categories ...)
- Pending Section : contains the table to show PENDING info about the table of its kind like (users,categories ...)
- Add Section : to transfer data into **Insert Section** tables from forms
- Insert Section : to make a validation on data which came from **Add Section** and insert it into database if it is valid
- Edit Section : to transfer data into **Update Section** tables from forms
- Insert Section : to make a validation on data which came from **Edit Section** and Edit it in database if it is valid
- Delete Section : to delete data from database tables
- Activate Section : to activate some data which it needs to activate like users and items
- Deactivate Section : to deactivate some data which it needs to deactivate like users and items

and each script may have its own specific sections.

#### Admin Logout Script:

it is a script has a simple unset for the admin session and redirect to index.php (login page) like below:

```
<?php

ob_start();

session_start();

unset($_SESSION["admin"]);
unset($_SESSION["admin_session_id"]);

header("Location: index.php");

exit();

ob_end_flush();

```

### MAIN SITE:

it includes eight scripts:

1. [`init.php`]("#init-page")
2. [`index.php`]("#index-page")
3. [`categories.php`]("#categories-page")
4. [`items.php`]("#items-page")
5. [`checkout.php`]("#checkout-page")
6. [`login.php`]("#login-page")
7. [`logout.php`]("#logout-page")
8. [`register.php`]("#register-page")

and also includes three API Urls:

1. [`cart.php`]("#cart-api")
2. [`comments.php`]("#comments-api")
3. [`likes.php`]("#likes-api")
