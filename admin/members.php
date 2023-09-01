<?php

session_start();
if (isset($_SESSION["admin"])) {
    $page_title = 'Members';
    include "init.php";
    include $tpl . "aside.php";

    // the content

    $do = isset($_GET['do']) ? $_GET['do'] : "Manage";

    switch ($do) {

        case "Manage":

            $stmt = $conn->prepare("SELECT user_id,username,full_name,email,reg_status,dt FROM users WHERE group_id != 1 ORDER BY username");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            $count = $stmt->rowCount();

?>

            <div class="container flow">
                <h1>Members</h1>
                <div class="table-container flow">
                    <table class="table" id="members-table" cellpadding="0px" cellspacing="0px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Name</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Register Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows as $row) {
                                $status = $row["reg_status"] ? "Activated" : "Not Activated";
                                $dimmed_class = $row["reg_status"] ? "" : "dimmed";
                                echo "<tr class='" . $dimmed_class . "'>";
                                echo "<td>" . $row["user_id"] . "</td>";
                                echo "<td>" . $row["username"] . "</td>";
                                echo "<td>" . $row["full_name"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>" . $status . "</td>";
                                echo "<td>" . $row["dt"] . "</td>";
                                echo    "<td class='dots'>";
                                echo        "<div class='list'>";
                                echo           $row["reg_status"] ? "" :  "<a class='btn btn-secondary confirm' href='?do=Activate&userid="   . $row["user_id"] . "'>Activate</a>";
                                echo          !$row["reg_status"] ? "" :  "<a class='btn btn-secondary confirm' href='?do=Deactivate&userid=" . $row["user_id"] . "'>Deactivate</a>";
                                echo           "<a class='btn btn-secondary' href='?do=Edit&userid=" . $row["user_id"] . "'>Edit</a>";
                                if (!hasDependencies("items", "user_id = " . $row["user_id"])) {
                                    echo "<a class='btn btn-secondary confirm' href='?do=Delete&userid=" . $row["user_id"] . "'>Delete</a>";
                                };
                                echo        "</div>";
                                echo    "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size: var(--fs-sm);"><?php echo $count . " users was found." ?></p>
                <a class="add-new-btn btn btn-primary" href='?do=Add' title="Add New Member"></a>
            </div>

            <script>
                let btns = document.querySelectorAll(".confirm");
                btns.forEach(confirmBtn => {
                    confirmBtn.addEventListener("click", (e) => {
                        const method = e.target.innerHTML;
                        const input = confirm(`Do you want actually to ${method.toUpperCase()} ${confirmBtn.parentElement.parentElement.parentElement.querySelector("td:nth-of-type(2)").innerHTML}?`);
                        !input && e.preventDefault();
                    });
                })
            </script>

        <?php
            break;

        case "Pending":

            $stmt = $conn->prepare("SELECT user_id,username,full_name,email,reg_status,dt FROM users WHERE group_id != 1 AND reg_status = 0 ORDER BY username");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            $count = $stmt->rowCount();

        ?>

            <div class="container flow">
                <h1>Members</h1>
                <div class="table-container flow">
                    <table class="table" id="members-table" cellpadding="0px" cellspacing="0px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Name</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Register Date</th>
                                <th>Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows as $row) {
                                $status = $row["reg_status"] ? "Activated" : "Not Activated";
                                $dimmed_class = $row["reg_status"] ? "" : "dimmed";
                                echo "<tr class='" . $dimmed_class . "'>";
                                echo "<td>" . $row["user_id"] . "</td>";
                                echo "<td>" . $row["username"] . "</td>";
                                echo "<td>" . $row["full_name"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>" . $status . "</td>";
                                echo "<td>" . $row["dt"] . "</td>";
                                echo    "<td class='dots'>";
                                echo        "<div class='list'>";
                                echo           $row["reg_status"] ? "" :  "<a class='btn btn-secondary confirm' href='?do=Activate&userid="   . $row["user_id"] . "'>Activate</a>";
                                echo          !$row["reg_status"] ? "" :  "<a class='btn btn-secondary confirm' href='?do=Deactivate&userid=" . $row["user_id"] . "'>Deactivate</a>";
                                echo           "<a class='btn btn-secondary' href='?do=Edit&userid=" . $row["user_id"] . "'>Edit</a>";
                                echo           "<a class='btn btn-secondary confirm' href='?do=Delete&userid=" . $row["user_id"] . "'>Delete</a>";
                                echo        "</div>";
                                echo    "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size: var(--fs-sm);"><?php echo $count . " users was found." ?></p>
                <a class="add-new-btn btn btn-primary" href='?do=Add'>Add New Member</a>
            </div>

            <script>
                let btns2 = document.querySelectorAll(".confirm");
                btns2.forEach(confirmBtn => {
                    confirmBtn.addEventListener("click", (e) => {
                        const method = e.target.innerHTML;
                        const input = confirm(`Do you want actually to ${method.toUpperCase()} ${confirmBtn.parentElement.parentElement.querySelector("td:nth-of-type(2)").innerHTML}?`);
                        !input && e.preventDefault();
                    });
                })
            </script>

        <?php
            break;

        case "Delete":
            // Delete Page Content
            $userid = isset($_GET["userid"]) && is_numeric($_GET["userid"]) ? $_GET["userid"] : 0;
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
            $stmt->execute(array($userid));
            $count = $stmt->rowCount();

            if ($count > 0) {
                $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->execute(array($userid));
                $msg =  "One user has been Deleted";
                redirect($msg, "back", 1, "success");
            } else {
                $msg = "there's no user like this";
                redirect($msg, "back", 1, "danger");
            }

            break;

        case "Activate":
            // Activate Page Content

            $userid = isset($_GET["userid"]) && is_numeric($_GET["userid"]) ? $_GET["userid"] : 0;
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
            $stmt->execute(array($userid));
            $count = $stmt->rowCount();

            if ($count > 0) {
                $stmt = $conn->prepare("UPDATE users SET reg_status = 1 WHERE user_id = ?");
                $stmt->execute(array($userid));
                $msg =  "One user has been Activated";
                redirect($msg, "back", 1, "success");
            } else {
                $msg = "there's no user like this";
                redirect($msg, "back", 1, "danger");
            }

            break;

        case "Deactivate":
            // Deactivate Page Content

            $userid = isset($_GET["userid"]) && is_numeric($_GET["userid"]) ? $_GET["userid"] : 0;
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
            $stmt->execute(array($userid));
            $count = $stmt->rowCount();

            if ($count > 0) {
                $stmt = $conn->prepare("UPDATE users SET reg_status = 0 WHERE user_id = ?");
                $stmt->execute(array($userid));
                $msg =  "One user has been Deactivated";
                redirect($msg, "back", 1, "success");
            } else {
                $msg = "there's no user like this";
                redirect($msg, "back", 1, "danger");
            }

            break;

        case "Insert":

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                echo "<h1 style='font-size:var(--fs-xl);text-align:center;margin-bottom:1em'>Insert Page</h1>";

                extract($_POST);
                $errors = array();

                $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
                $stmt->execute(array($username));
                $count = $stmt->rowCount();

                $count > 0 && $errors[] = "<strong>Username</strong> has been registered already";
                !preg_match($username_re, $username) && $errors[] = "<strong>UserName</strong> must start with small letter, it must be at least 4 characters and less than or equal 20 characters, it can include digits and period symbol only";
                !preg_match($email_re, $email) && $errors[] = "<strong>Email</strong> not valid";
                !preg_match($password_re, $password) && $errors[] = "<strong>Password</strong> must be more than 8 characters";
                !preg_match($full_name_re, $fullname) && $errors[] = "<strong>Fullname</strong> must start with letter and can it has spaces and ends with letter";

                echo "<ul class='error-msgs'>";
                foreach ($errors as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul>";

                if (count($errors) === 0) {
                    $stmt = $conn->prepare("INSERT INTO users(username, password, email, full_name, dt) VALUES (?,?,?,?,NOW())");
                    $stmt->execute(array($username, sha1($password), $email, $fullname));
                    $msg =  $stmt->rowCount() . " User has been Added";
                    redirect($msg, "members.php", 1, $type = "success");
                }
            } else {
                $msg = "You can't browse this page directly";
                redirect($msg, "members.php", 1, "danger");
            }
            break;

        case "Add":
            // Add Page Content
            $main_heading = "Add new Member";
        ?>

            <div class="container">
                <h1>Add New Member</h1>
                <form action="?do=Insert" method="POST" class="form flow" id="add-members-form">
                    <ul class="msgs" id="add-members-form-messages">
                    </ul>
                    <div class="inputs fields">
                        <div class="form-input">
                            <input type="text" name="username" id="add-members-username" placeholder="username" autocomplete="off" required>
                            <label for="add-members-username">Username</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="fullname" id="add-members-fullname" placeholder="Full Name" autocomplete="off" required>
                            <label for="add-members-fullname">Full Name</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="email" id="add-members-email" placeholder="Email" autocomplete="off" required>
                            <label for="add-members-email">Email</label>
                        </div>
                        <div class="form-input">
                            <input type="password" name="password" id="add-members-password" placeholder="password" autocomplete="new-password" required>
                            <label for="add-members-password">Password</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="add-members-submit">Add Member</button>
                </form>
            </div>

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
                } from "./layout/js/formsValidation.js";
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

                <div class="container">
                    <h1>Edit <?php echo isset($full_name) ? $full_name : "Member" ?> Information</h1>
                    <form action="?do=Update" method="POST" class="form flow" id="edit-members-form">
                        <ul class="msgs" id="members-form-messages">
                        </ul>
                        <div class="inputs fields">
                            <input type="hidden" name="userid" value="<?php echo $userid ?>">
                            <div class="form-input">
                                <input type="text" name="username" id="edit-members-username" placeholder="username" autocomplete="off" value=<?php echo isset($username) ? $username : "" ?> required>
                                <label for="edit-members-username">Username</label>
                            </div>
                            <div class="form-input">
                                <input type="text" name="fullname" id="edit-members-fullname" placeholder="Full Name" autocomplete="off" value="<?php echo isset($full_name) ? trim($full_name) : "" ?>" required>
                                <label for="edit-members-fullname">Full Name</label>
                            </div>
                            <div class="form-input">
                                <input type="text" name="email" id="edit-members-email" placeholder="Email" autocomplete="off" value=<?php echo isset($email) ? $email : "" ?> required>
                                <label for="edit-members-email">Email</label>
                            </div>
                            <div class="form-input">
                                <input type="password" name="password" id="edit-members-password" placeholder="password" autocomplete="new-password" required>
                                <label for="edit-members-password">Password</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="edit-members-submit">Update</button>
                    </form>
                </div>

                <script>
                    const passInputAdd = document.getElementById("add-members-password");
                    if (passInputAdd) {
                        passInputAdd.addEventListener("mouseenter", () => {
                            passInputAdd.setAttribute("type", "text");
                        })
                        passInputAdd.addEventListener("mouseout", () => {
                            passInputAdd.setAttribute("type", "password");
                        })
                    }
                </script>

                <script type="module">
                    import {
                        Validate
                    } from "./layout/js/formsValidation.js";
                    let editForm = new Validate(
                        "edit-members-form",
                        "members-form-messages",
                        "edit-members-submit"
                    );
                    editForm.submitForm();
                </script>

<?php
            } /* end of if statement */ else {
                $msg = "there is no user like this";
                redirect($msg, "members.php", 1, "danger");
            }

            break;
        case "Update":

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                echo "<h1 style='font-size:var(--fs-xl);text-align:center;margin-bottom:1em'>Update Page</h1>";
                extract($_POST);
                $errors = array();

                $stmt = $conn->prepare("SELECT username FROM users WHERE user_id != ? AND username = ? LIMIT 1");
                $stmt->execute(array($userid, $username));
                $user_exists = $stmt->rowCount();

                $user_exists && $errors[] = "<strong>Username</strong> already found";
                !preg_match($username_re, $username) && $errors[] = "<strong>UserName</strong> must start with small letter, it must be at least 4 characters and less than or equal 20 characters, it can include digits and period symbol only";
                !preg_match($email_re, $email) && $errors[] = "<strong>Email</strong> not valid";
                !preg_match($password_re, $password) && $errors[] = "<strong>Password</strong> must be more than 8 characters";
                !preg_match($full_name_re, $fullname) && $errors[] = "<strong>Fullname</strong> must start with letter and can it has spaces and ends with letter";

                echo "<ul class='error-msgs'>";
                foreach ($errors as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul>";

                if (count($errors) === 0) {
                    $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ? , password = ? WHERE user_id = ?");
                    $stmt->execute(array($username, $fullname, $email, sha1($password), $userid));
                    echo $stmt->rowCount() . " User has been Updated";
                }
            } else {
                $msg = "You can't browse this page directly";
                redirect($msg, "members.php", 1, "danger");
            }
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
