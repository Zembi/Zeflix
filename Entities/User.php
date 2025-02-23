<?php

require_once './Tools/HandleInternalMsgs.php';
require_once './Models/User.php';
require_once './Tools/FormSanitizer.php';
require_once './Tools/FormValidator.php';

class User {
    private User_Model $user_model;
    private string $username;
    private string $first_name;
    private string $last_name;
    private string $email;
    private string $password;

    private string $session_token;

//  CONSTRUCT
    public function __construct(PDO|null $db_conn) {
        if($db_conn) $this->initDbConnection($db_conn);
    }

//  GETTERS
    public function getUsername() : string {
        return $this->username;
    }
    public function getFirstName(): string {
        return $this->first_name;
    }
    public function getLastName(): string {
        return $this->last_name;
    }
    public function getEmail(): string {
        return $this->email;
    }
    public function getPassword(): string {
        return $this->password;
    }

    public function getSessionToken(): string {
        return $this->session_token;
    }

//  SETTERS
    private function setUsername(string $username): void {
        $this->username = $username;
    }
    private function setFirstName(string $first_name): void {
        $this->first_name = $first_name;
    }
    private function setLastName(string $last_name): void {
        $this->last_name = $last_name;
    }
    private function setEmail(string $email): void {
        $this->email = $email;
    }
    private function setPassword(string $password): void {
        $this->password = $password;
    }

    private function setSessionToken(string $session_token): void {
        $this->session_token = $session_token;
    }


//  HASHING PASSWORD
    public function hashPassword() : string {
        return password_hash($this->getPassword(), PASSWORD_ARGON2ID);
    }

    private function confirmHashedPasswordMatch(string $password) : bool {
        $db_hashed_password = $this->user_model->get_password($this->getUsername());
        return !$db_hashed_password ? false : password_verify($password, $db_hashed_password);
    }


    public function initDbConnection(PDO $db_conn): void {
        $this->user_model = new User_Model($db_conn);
    }

    public function usernameFound(string $username): bool {
        return $this->user_model->available_username($username);
    }

    public function emailFound(string $email): bool {
        return $this->user_model->available_email($email);
    }

    public function register(array $data): array {
        $username = $data['username'];
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $email = $data['email'];
        $email2 = $data['email2'];
        $password = $data['password'];
        $password2 = $data['password2'];

//      STRING VALIDATIONS
        $firstName = FormSanitizer::sanitizeFormString($firstName);
        $firstName = FormValidator::stringGreaterThan($firstName);

        $lastName = FormSanitizer::sanitizeFormString($lastName);
        $lastName = FormValidator::stringGreaterThan($lastName);

        $username = FormSanitizer::sanitizeUsername($username);
        $username_in_range = FormValidator::stringGreaterThan($username, 5, 20);

        $email = FormSanitizer::sanitizeFormString($email);
        $email2 = FormSanitizer::sanitizeFormString($email2);
        $email_valid = FormValidator::validEmail($email);
        $email_in_range = FormValidator::stringGreaterThan($email, 0, 60);
        $email_diff = FormValidator::confirmEmail($email, $email2);

        $password = FormSanitizer::sanitizePassword($password);
        $password2 = FormSanitizer::sanitizePassword($password2);
        $password_in_range = FormValidator::stringGreaterThan($password, 6, 14);
        $password_diff = FormValidator::confirmPassword($password, $password2);

//      FINAL VALIDATIONS BEFORE USING QUERIES
        $errors = [];

        if(!$firstName) $errors['firstName_in_range'] = 'First name must be between 3 and 24 characters';

        if(!$lastName) $errors['lastName_in_range'] = 'Last name must be between 3 and 24 characters';

        if(!$username_in_range) $errors['username_in_range'] = 'Username must be between 5 and 20 characters';

        if(!$email_valid) $errors['email_non_valid_form'] = 'Email is invalid';
        if(!$email_in_range) $errors['email_in_range'] = 'Email must not exceed 60 characters';
        if(!$email_diff) $errors['email_diff'] = 'Email addresses don\'t match';

        if(!$password_in_range) $errors['password_in_range'] = 'Password must be between 6 and 14 characters';
        if(!$password_diff) $errors['password_diff'] = 'Passwords don\'t match';

//      EXIT HERE IF ERRORS FOUND
        if(!empty($errors)) return HandleInternalMsgs::errorMsgOnReturn($errors);

//      VALIDATION AND ERROR HANDLING USING QUERIES
        $username_found = $this->usernameFound($username);
        if($username_found) $errors['username_found'] = 'Username already exists';

        $email_found = $this->emailFound($email);
        if($email_found) $errors['email_found'] = 'Email already in use';

//      EXIT HERE IF ERRORS FOUND
        if(!empty($errors)) return HandleInternalMsgs::errorMsgOnReturn($errors);

        $this->setUsername($username);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPassword($password);

        return $this->user_model->register_user($this);
    }

    public function login(array $data): array {
//      HANDLE LOGIN FROM HERE
        $username = $data['username'];
        $password = $data['password'];

        $username = FormSanitizer::sanitizeFormString($username, true);
        $password = FormSanitizer::sanitizePassword($password);

        $sanitized_data = [
            'username' => $username,
            'password' => $password
        ];

//      QUERY VALIDATIONS
        $errors = [];

        $username_found = $this->usernameFound($username);
        if(!$username_found) $errors['username_not_found'] = 'Username doesn\'t exists';

//      EXIT HERE IF ERRORS FOUND
        if(!empty($errors)) return HandleInternalMsgs::errorMsgOnReturn($errors);

        $this->setUsername($username);

        $password_confirmed = $this->confirmHashedPasswordMatch($password);
        if(!$password_confirmed) $errors['password_wrong'] = 'Passwords is incorrect';

//      EXIT HERE IF ERRORS FOUND
        if(!empty($errors)) return HandleInternalMsgs::errorMsgOnReturn($errors);

        return $this->user_model->sign_in_user($sanitized_data);
    }
}