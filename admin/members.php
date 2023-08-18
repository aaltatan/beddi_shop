<?php

session_start();
if (isset($_SESSION["username"])) {
    $page_title = 'Members';
    include "init.php";
    include $tpl . "aside.php";

    // the content

    $do = isset($_GET['do']) ? $_GET['do'] : "Manage";

    switch ($do) {

        case "Manage": ?>


            <div class="table-container flow">
                <table class="table" id="members-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Register Date</th>
                            <th>Control</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT user_id,username,full_name,email FROM users");
                        $stmt->execute();
                        $data = $stmt->fetchAll();
                        foreach ($data as $row) {
                            echo "<tr>";
                            echo "<td>" . $row["user_id"] . "</td>";
                            echo "<td>" . $row["username"] . "</td>";
                            echo "<td>" . $row["full_name"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>" . "12-5-2023" . "</td>";
                            echo "<td class='btn-group'><a class='btn btn-primary' href='#'>Edit</a><a class='btn btn-danger' href='#'>Delete</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <a class="btn btn-primary" href='?do=Add'>Add New Member</a>

        <?php
            break;

        case "Insert":

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                echo "<h1 style='font-size:var(--fs-xl);text-align:center;margin-bottom:1em'>Insert Page</h1>";

                extract($_POST);
                $errors = array();

                $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
                $stmt->execute(array($username));
                $count = $stmt->rowCount();

                $count > 0 && $errors[] = "this name has been registered already";
                strlen($username) < 4 && $errors[] = "User Name must be more than 4 characters";
                strlen($username) > 20 && $errors[] = "User Name must be less than or equal 20 characters";
                $re = '/^[A-Za-z0-9\.\-_]+@[a-z0-9\-]+\.\w{2,4}$/';
                !preg_match($re, $email) && $errors[] = "Email not valid";
                strlen($password) < 8 && $errors[] = "Password must be more than 8 characters";
                echo "<ul style='padding-left:1em;'>";
                foreach ($errors as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul>";
                if (count($errors) === 0) {
                    $stmt = $conn->prepare("INSERT INTO users(username, password, email, full_name) VALUES (?,?,?,?)");
                    $stmt->execute(array($username, sha1($password), $email, $fullname));
                    echo $stmt->rowCount() . " User has been Added";
                    header("Location: members.php");
                    exit();
                }
            } else {
                echo "You can't browse this page directly";
            }
            break;

        case "Add":
            // Add Page Content
            $main_heading = "Add new Member";
        ?>

            <form action="?do=Insert" method="POST" class="form flow" id="add-members-form">
                <h1>Add New Member</h1>
                <ul class="msgs" id="add-members-form-messages">
                </ul>
                <div class="inputs">
                    <div class="form-input">
                        <input type="text" name="username" id="add-members-username" placeholder="username" autocomplete="off">
                        <label for="add-members-username">Username</label>
                    </div>
                    <div class="form-input">
                        <input type="text" name="fullname" id="add-members-fullname" placeholder="Full Name" autocomplete="off" ">
                        <label for=" add-members-fullname">Full Name</label>
                    </div>
                    <div class="form-input">
                        <input type="text" name="email" id="add-members-email" placeholder="Email" autocomplete="off">
                        <label for="add-members-email">Email</label>
                    </div>
                    <div class="form-input">
                        <input type="password" name="password" id="add-members-password" placeholder="password" autocomplete="new-password">
                        <label for="add-members-password">Password</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="add-members-submit">Add Member</button>
            </form>

            <script>
                const passInput = document.getElementById("add-members-password");
                passInput.addEventListener("mouseenter", () => {
                    passInput.setAttribute("type", "text");
                })
                passInput.addEventListener("mouseout", () => {
                    passInput.setAttribute("type", "password");
                })
            </script>

            <script type="module">
                import {
                    Validate
                } from "../layout/js/formsValidation.js";
                let addForm = new Validate(
                    "add-members-form",
                    "add-members-form-messages",
                    "add-members-submit"
                );
                addForm.submitForm();
            </script>

            <?php
            break;

        case "Edit":
            // Edit Page Content

            $userid = isset($_GET["userid"]) && is_numeric($_GET["userid"]) ? $_GET["userid"] : 0;
            $query = $conn->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
            $query->execute(array($userid));
            $data = $query->fetch();
            $count = $query->rowCount();
            if ($count > 0) {
                $username = $data["username"];
                $email = $data["email"];
                $full_name = $data["full_name"];
            ?>

                <form action="?do=Update" method="POST" class="form flow" id="edit-members-form">
                    <h1>Edit <?php echo isset($full_name) ? $full_name : "Member" ?> Information</h1>
                    <ul class="msgs" id="members-form-messages">
                    </ul>
                    <div class="inputs">
                        <input type="hidden" name="userid" value="<?php echo $userid ?>">
                        <div class="form-input">
                            <input type="text" name="username" id="edit-members-username" placeholder="username" autocomplete="off" value=<?php echo isset($username) ? $username : "" ?>>
                            <label for="edit-members-username">Username</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="fullname" id="edit-members-fullname" placeholder="Full Name" autocomplete="off" value="<?php echo isset($full_name) ? trim($full_name) : "" ?>">
                            <label for="edit-members-fullname">Full Name</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="email" id="edit-members-email" placeholder="Email" autocomplete="off" value=<?php echo isset($email) ? $email : "" ?>>
                            <label for="edit-members-email">Email</label>
                        </div>
                        <div class="form-input">
                            <input type="password" name="password" id="edit-members-password" placeholder="password" autocomplete="new-password">
                            <label for="edit-members-password">Password</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="edit-members-submit">Update</button>
                </form>

                <script>
                    const passInputAdd = document.getElementById("add-members-password");
                    passInput.addEventListener("mouseenter", () => {
                        passInput.setAttribute("type", "text");
                    })
                    passInput.addEventListener("mouseout", () => {
                        passInput.setAttribute("type", "password");
                    })
                </script>

                <script type="module">
                    import {
                        Validate
                    } from "../layout/js/formsValidation.js";
                    let editForm = new Validate(
                        "edit-members-form",
                        "members-form-messages",
                        "edit-members-submit"
                    );
                    form.submitForm();
                </script>

<?php
            } /* end of if statement */ else {
                echo "there is no user like this";
            }

            break;
        case "Update":
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                echo "<h1 style='font-size:var(--fs-xl);text-align:center;margin-bottom:1em'>Update Page</h1>";
                extract($_POST);
                $errors = array();
                strlen($username) < 4 && $errors[] = "User Name must be more than 4 characters";
                strlen($username) > 20 && $errors[] = "User Name must be less than or equal 20 characters";
                $re = '/^[A-Za-z0-9\.\-_]+@[a-z0-9\-]+\.\w{2,4}$/';
                !preg_match($re, $email) && $errors[] = "Email not valid";
                strlen($password) < 8 && $errors[] = "Password must be more than 8 characters";
                echo "<ul style='padding-left:1em;'>";
                foreach ($errors as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul>";
                if (count($errors) === 0) {
                    $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ? , password = ? WHERE user_id = ?");
                    $stmt->execute(array($username, $fullname, $email, sha1($password), $userid));
                    echo $stmt->rowCount() . " User has been Updated";
                    header("Location: logout.php");
                    exit();
                }
            } else {
                echo "You can't browse this page directly";
            }
            break;
        default:
            echo "there is no page like $do";
    }

    include $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit();
}