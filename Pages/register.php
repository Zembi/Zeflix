<?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/db/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/Entities/User.php';

    require_once $_SERVER['DOCUMENT_ROOT'].'/Templates/NotificationMsg.php';

    global $db_conn;
    global $route;

    $session = new Session($db_conn, $route);
    $user = new User($db_conn);

    if(isset($_POST['submitRegister'])) {
        $form_data = [
            'firstName' => $_POST['firstName'],
            'lastName' => $_POST['lastName'],
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'email2' => $_POST['email2'],
            'password' => $_POST['password'],
            'password2' => $_POST['password2'],
        ];
        $register_response = $user->register($form_data);
        $session->collectRegisterResponseIntoSession($register_response);
    }

    $response = $session->handleRegisterResponseFromSession();

    $errors = $response['errors'];

    $last_inputs = $response['last_inputs'];
    $firstName = htmlspecialchars($last_inputs['firstName'] ?? '');
    $lastName = htmlspecialchars($last_inputs['lastName'] ?? '');
    $username = htmlspecialchars($last_inputs['username'] ?? '');
    $email = htmlspecialchars($last_inputs['email'] ?? '');
    $email2 = htmlspecialchars($last_inputs['email2'] ?? '');

    $success_message = htmlspecialchars($response['success']);
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Welcome to Zeflix</title>
        <link rel="stylesheet" type="text/css" href="../assets/style/style.css" />
    </head>
    <body>
        <?php
        if(!empty($success_message)) {
            NotificationMsg::successfulNotify(htmlspecialchars($success_message));
            $session->successfulSingIn($user);
        }
        else if(count($errors) > 0) {
            NotificationMsg::errorNotify('One or more errors are found in the register form');
        }
        ?>
        <div class="sing-in-container">
            <div class="column">
                <img class="zeflix-logo" src="../assets/images/zeflix_image.png" title="Zeflix" alt="zeflix logo" />
                <h2 class="title">Sign Up</h2>
                <span class="subtitle simple-text">to continue to VideoTube</span>
                <form class="register-form account-entry-form" method="POST" action="">
                    <label class="form-label-field">
                        <input type="text" name="firstName" class="simple-text<?= isset($errors['firstName']) ? ' error_field' : '' ?>" placeholder="First name" value="<?= $firstName ?>" required/>
                        <?php if(isset($errors['firstName'])) { ?>
                            <span class="error-form-msg"><?= $errors['firstName'] ?></span>
                        <?php } ?>
                    </label>
                    <label class="form-label-field">
                        <input type="text" name="lastName" class="simple-text<?= isset($errors['lastName']) ? ' error_field' : '' ?>" placeholder="Last name" value="<?= $lastName ?>" required/>
                        <?php if(isset($errors['lastName'])) { ?>
                            <span class="error-form-msg"><?= $errors['lastName'] ?></span>
                        <?php } ?>
                    </label>
                    <label class="form-label-field">
                        <input type="text" name="username" class="simple-text<?= isset($errors['username']) ? ' error_field' : '' ?>" placeholder="Username" value="<?= $username ?>" required/>
                        <?php if(isset($errors['username'])) { ?>
                            <span class="error-form-msg"><?= $errors['username'] ?></span>
                        <?php } ?>
                    </label>
                    <label class="form-label-field">
                        <input type="email" name="email" class="simple-text<?= isset($errors['email']) ? ' error_field' : '' ?>" placeholder="Email" value="<?= $email ?>" required/>
                        <?php if(isset($errors['email'])) { ?>
                            <span class="error-form-msg"><?= $errors['email'] ?></span>
                        <?php } ?>
                    </label>
                    <label class="form-label-field">
                        <input type="email" name="email2" class="simple-text<?= isset($errors['email']) || isset($errors['email2']) ? ' error_field' : '' ?>" placeholder="Confirm email" value="<?= $email2 ?>" required/>
                        <?php if(isset($errors['email2'])) { ?>
                            <span class="error-form-msg"><?= $errors['email2'] ?></span>
                        <?php } ?>
                    </label>
                    <label class="form-label-field">
                        <input type="password" name="password" class="simple-text<?= isset($errors['password']) ? ' error_field' : '' ?>" placeholder="Password" required/>
                        <?php if(isset($errors['password'])) { ?>
                            <span class="error-form-msg"><?= $errors['password'] ?></span>
                        <?php } ?>
                    </label>
                    <label class="form-label-field">
                        <input type="password" name="password2" class="simple-text<?= isset($errors['password']) || isset($errors['password2']) ? ' error_field' : '' ?>" placeholder="Confirm password" required/>
                        <?php if(isset($errors['password2'])) { ?>
                            <span class="error-form-msg"><?= $errors['password2'] ?></span>
                        <?php } ?>
                    </label>

                    <input type="submit" name="submitRegister" class="simple-text" value="Register"/>
                </form>
                <span>Already have an account? <a href="./Login.php">Sign in here!</a></span>
            </div>
        </div>
    </body>
</html>