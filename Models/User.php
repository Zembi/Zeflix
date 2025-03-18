<?php

class User_Model extends Model {
//  FINDERS
    public function available_username(string $username): bool {
        $query = $this->db->prepare("
            SELECT username
            FROM users
            WHERE username = :username");
        $query->bindParam(":username", $username);

        $executed = $query->execute();

        if($executed && $query->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function available_email(string $email): bool {
        $query = $this->db->prepare("
            SELECT email
            FROM users
            WHERE email = :email
        ");
        $query->bindParam(":email", $email);

        $executed = $query->execute();

        if($executed && $query->rowCount() > 0) {
            return true;
        }
        return false;
    }


//   RETRIEVERS
    public function get_password(string $username): array {
        $query = $this->db->prepare("
            SELECT password
            FROM users
            WHERE username = :username
        ");
        $query->bindParam(":username", $username);

        $executed = $query->execute();

        if($executed && $query->rowCount() > 0) {
            $final_data = $query->fetch(PDO::FETCH_ASSOC);
            return HandleInternalMsgs::succesMsgOnReturn($final_data);
        }
        return HandleInternalMsgs::errorMsgOnReturn(['msg' => 'Username hasn\'t been registered']);
    }



    public function register_user(User $user): array {
        $this->setGreekTimezoneBeforeQuery();
        $query = $this->db->prepare("
            INSERT INTO users (first_name, last_name, username, email, password) 
            VALUES (:first_name, :last_name, :username, :email, :password)
        ");
        $executed = $query->execute([
            ':first_name' => $user->getFirstName(),
            ':last_name'  => $user->getLastName(),
            ':username'   => $user->getUsername(),
            ':email'      => $user->getEmail(),
            ':password'   => $user->hashPassword()
        ]);

        $last_id = $this->db->lastInsertId();

        if($executed && $query->rowCount() > 0) {
            return HandleInternalMsgs::succesMsgOnReturn(['id' => $last_id]);
        }
        return HandleInternalMsgs::errorMsgOnReturn(['id' => $last_id]);
    }

    public function sign_in_user(array $data): array {
        return HandleInternalMsgs::succesMsgOnReturn($data);
    }

    public function fetch_user(array $data): array {
        if(isset($data['username'])) {
            $query = $this->db->prepare("
                SELECT *
                FROM users
                WHERE username = :username
            ");
            $query->bindParam(":username", $data['username']);
        }
        else if(isset($data['email'])) {
            $query = $this->db->prepare("
                SELECT *
                FROM users
                WHERE email = :email
            ");
            $query->bindParam(":email", $data['email']);
        }
        else {
            return HandleInternalMsgs::errorMsgOnReturn(['msg' => 'Invalid parameter properties - username or email properties haven\'t been found']);
        }

        $executed = $query->execute();

        if($executed && $query->rowCount() > 0) {
            $final_data = $query->fetch(PDO::FETCH_ASSOC);
            return HandleInternalMsgs::succesMsgOnReturn($final_data);
        }
        return HandleInternalMsgs::errorMsgOnReturn(['msg' => 'Invalid username or email']);
    }
}