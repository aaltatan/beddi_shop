<?php
session_start();
$page_title = "Log in";
if (isset($_SESSION["admin"])) {
    header("Location: dashboard.php");
    exit();
}
include "init.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST["user"];
    $password = $_POST["pass"];
    $hashed_pass = sha1($password);
    $query = $conn->prepare("SELECT 
                                user_id,username,password 
                            FROM 
                                users 
                            WHERE 
                                username = ? 
                            AND 
                                password = ? 
                            AND 
                                group_id = 1");
    $query->execute(array($username, $hashed_pass));
    $count = $query->rowCount();

    if ($count > 0) {
        $user_id = $query->fetchAll()[0][0];
        $_SESSION["admin"] = $username;
        $_SESSION["adminid"] = $user_id;
        $_SESSION["admin_session_id"] = $user_id;
        header("Location: dashboard.php");
        exit();
    }
}
?>

<div class="login-bg">
    <form class="login" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
        <h1>Admin Login</h1>
        <input class="login-input" type="text" name="user" placeholder="enter your username" autocomplete="off">
        <input class="login-input" type="password" name="pass" placeholder="enter your password" autocomplete="new-password">
        <input type="submit" class="btn btn-primary" value="Log in">
    </form>
</div>

<script>
    const form = document.querySelector("form");
    const usernameRe = /[a-z]{3,}/;
    const passwordRe = /.{8,}/;
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        form.querySelectorAll("span").forEach(span => span.remove());
        const criteriaOne = usernameRe.test(form.querySelector("input[type='text']").value);
        const criteriaTwo = passwordRe.test(form.querySelector("input[type='password']").value);
        !criteriaOne && addMsg(form, "Username must at least 3 lower case characters");
        !criteriaTwo && addMsg(form, "Password must at least 8 characters");
        criteriaOne && criteriaTwo && e.currentTarget.submit();
    })

    function addMsg(form, msg) {
        const msgSpan = document.createElement("span");
        const msgTextNode = document.createTextNode(msg)
        msgSpan.appendChild(msgTextNode);
        form.appendChild(msgSpan)
    }
</script>