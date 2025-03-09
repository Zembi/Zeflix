<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/Models/Session.php';

class Session {
    private Session_Model $session_model;
    private Route $route;
    private ?User $user;

    public function __construct(PDO|null $db_conn, Route $route) {
        session_start();

        $this->route = $route;
        if($db_conn) $this->initDbConnection($db_conn);

        $this->setLastVisitedPage($this->route->getCurrentPageName());

        $this->user = null;
    }

    public function initDbConnection(PDO $db_conn): void {
        $this->session_model = new Session_Model($db_conn);
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }

    public function collectRegisterResponseIntoSession(array $register_response): void {
        if(!$register_response['status']) {
            $_SESSION['register_errors'] = [];

//          FIRST NAME CONFIGURE VALIDATION MESSAGE
            if(isset($register_response['response']['firstName_in_range'])) {
                $_SESSION['register_errors']['firstName'] = $register_response['response']['firstName_in_range'];
            }

//          LAST NAME CONFIGURE VALIDATION MESSAGE
            if(isset($register_response['response']['lastName_in_range'])) {
                $_SESSION['register_errors']['lastName'] = $register_response['response']['lastName_in_range'];
            }

//          USERNAME CONFIGURE VALIDATION MESSAGE
            if(isset($register_response['response']['username_found'])) {
                $_SESSION['register_errors']['username'] = $register_response['response']['username_found'];
            }
            if(isset($register_response['response']['username_in_range'])) {
                $_SESSION['register_errors']['username'] = $register_response['response']['username_in_range'];
            }

//          EMAIL CONFIGURE VALIDATION MESSAGE
            if(isset($register_response['response']['email_in_range'])) {
                $_SESSION['register_errors']['email'] = $register_response['response']['email_in_range'];
            }
            if(isset($register_response['response']['email_non_valid_form'])) {
                $_SESSION['register_errors']['email'] = $register_response['response']['email_non_valid_form'];
            }
            if(isset($register_response['response']['email_found'])) {
                $_SESSION['register_errors']['email'] = $register_response['response']['email_found'];
            }
            if(isset($register_response['response']['email_diff'])) {
                $_SESSION['register_errors']['email2'] = $register_response['response']['email_diff'];
            }

//          PASSWORD CONFIGURE VALIDATION MESSAGE
            if(isset($register_response['response']['password_in_range'])) {
                $_SESSION['register_errors']['password'] = $register_response['response']['password_in_range'];
            }
            if(isset($register_response['response']['password_diff'])) {
                $_SESSION['register_errors']['password2'] = $register_response['response']['password_diff'];
            }
        }
        else {
            $_SESSION['register_success'] = "You have been registered successfully!";
        }

        $_SESSION['old_register_input'] = $_POST;
    }

    public function handleRegisterResponseFromSession(): array {
        $response = [
            'last_inputs' => $_SESSION['old_register_input'] ?? [],
            'errors' => $_SESSION['register_errors'] ?? [],
            'success' => $_SESSION['register_success'] ?? ''
        ];

//      LEAVE OLD REGISTER INPUTS IN SESSION STORAGE
        unset($_SESSION['register_errors'], $_SESSION['register_success']);

        return $response;
    }


    public function collectLogInResponseIntoSession(array $login_response): void {
        if(!$login_response['status']) {
            $_SESSION['login_errors'] = [];

//          USERNAME CONFIGURE VALIDATION MESSAGE
            if(isset($login_response['response']['username_not_found'])) {
                $_SESSION['login_errors']['username'] = $login_response['response']['username_not_found'];
            }

//          PASSWORD CONFIGURE VALIDATION MESSAGE
            if(isset($login_response['response']['password_wrong'])) {
                $_SESSION['login_errors']['password'] = $login_response['response']['password_wrong'];
            }
        }
        else {
            $_SESSION['login_success'] = "You have been logged in successfully!";
        }

        $_SESSION['old_login_input'] = $_POST;
    }

    public function handleLogInResponseFromSession(): array {
        $response = [
            'last_inputs' => $_SESSION['old_login_input'] ?? [],
            'errors' => $_SESSION['login_errors'] ?? [],
            'success' => $_SESSION['login_success'] ?? ''
        ];

//      LEAVE OLD REGISTER INPUTS IN SESSION STORAGE
        unset($_SESSION['login_errors'], $_SESSION['login_success']);

        return $response;
    }

    private function clearVisitorsSessionData(): void {
        unset($_SESSION['old_register_input'], $_SESSION['register_errors'], $_SESSION['register_success']);
        unset($_SESSION['old_login_input'], $_SESSION['login_errors'], $_SESSION['login_success']);
    }


    public function successfulSingIn(User $user): void {
        $this->clearVisitorsSessionData();
        $this->setUser($user);

        $responseToken = $this->session_model->handle_user_session_token($_SESSION['user_token_logged_in'], $user, $this->route->getCurrentPage());
        if($responseToken && !empty($responseToken)) {
            $_SESSION['user_token_logged_in'] = $responseToken;
        }

        $this->route->redirectToRoot();
    }

    public function isUserLoggedInInSession(): bool {
        $user_token = $_SESSION['user_token_logged_in'] ?? null;

        if($user_token && !empty($user_token)) {
            $usernameFound = $this->session_model->confirm_user_session_token($user_token);
            if($usernameFound) {
                $user = $this->user->fetchUserDataFromDB(['username' => $usernameFound]);
                if($user) {
                    $this->user = $user;
                    return true;
                }
            }
        }
        return false;
    }

    public function setLastVisitedPage(string $lastVisitedPage): void {
        $_SESSION['last_visited_page'] = $lastVisitedPage;
    }
}