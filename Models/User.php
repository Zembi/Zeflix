<?php

class User_Model extends Model {
//  FINDERS
    public function available_username(string $username): bool {
        $query = $this->db->prepare("SELECT username FROM users WHERE username = :username");
        $query->bindParam(":username", $username);

        $executed = $query->execute();

        if($executed && $query->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function available_email(string $email): bool {
        $query = $this->db->prepare("SELECT email FROM users WHERE email = :email");
        $query->bindParam(":email", $email);

        $executed = $query->execute();

        if($executed && $query->rowCount() > 0) {
            return true;
        }
        return false;
    }


//   RETRIEVERS
    public function get_password(string $username): ?string {
        $query = $this->db->prepare("SELECT password FROM users WHERE username = :username");
        $query->bindParam(":username", $username);

        $executed = $query->execute();

        if($executed && $query->rowCount() > 0) {
            return $query->fetchColumn();
        }
        return null;
    }



    public function register_user(User $user): array {
        $query = $this->db->prepare("INSERT INTO users (first_name, last_name, username, email, password) VALUES (:first_name, :last_name, :username, :email, :password)");
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
        return HandleInternalMsgs::errorMsgOnReturn(['id' => $last_id]);;
    }

    public function sign_in_user(array $data): array {
        return HandleInternalMsgs::succesMsgOnReturn($data);
    }
}