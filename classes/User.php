<?php
// ვინაიდან სამივე ფაილი ერთ საქაღალდეშია (classes), პირდაპირ შემოვუკიდოთ სახელით:
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Logger.php';

class User {
    private $db;
// ... აქედან კოდი ჩვეულებრივად გრძელდება უცვლელად ...
    

    public function __construct() {
        // ვიღებთ ბაზის ერთადერთ კავშირს (Singleton)
        $this->db = Database::getInstance();
    }

    // 1. მომხმარებლის რეგისტრაცია
    public function register($email, $password) {
        try {
            // ვამოწმებთ, ხომ არ არსებობს უკვე ეს იმეილი
            $query = "SELECT id FROM users WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['email' => $email]);

            if ($stmt->rowCount() > 0) {
                return "ეს ელ.ფოსტა უკვე დაკავებულია!";
            }

            // უსაფრთხოება: პაროლის ჰეშირება დაცვისთვის
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // მომხმარებლის ჩაწერა users ცხრილში
            $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'email' => $email,
                'password' => $hashed_password
            ]);

            // ვიღებთ ახლად შექმნილი მომხმარებლის ID-ს
            $user_id = $this->db->lastInsertId();

            // ავტომატურად ვანიჭებთ სტანდარტულ როლს: 'User' (რომლის ID-ც ბაზაში არის 2)
            $query = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, 2)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'user_id' => $user_id
            ]);

            // აქტივობის ჩაწერა ლოგ ფაილში
            Logger::log("წარმატებით დარეგისტრირდა სისტემაში", $email);
            return true;

        } catch (PDOException $e) {
            Logger::log("რეგისტრაციის შეცდომა: " . $e->getMessage(), $email);
            return "სისტემური შეცდომა რეგისტრაციისას.";
        }
    }

    // 2. მომხმარებლის ავტორიზაცია (Login)
    public function login($email, $password) {
        try {
            // ვეძებთ მომხმარებელს იმეილით
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            // თუ მომხმარებელი მოიძებნა და პაროლიც ემთხვევა ჰეშს
            if ($user && password_verify($password, $user['password'])) {
                
                // სესიის დაწყება და მონაცემების შენახვა
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
               $_SESSION['user_id'] = $user['id'];
               $_SESSION['user_email'] = $user['email'];

                // აქვე ამოვიღოთ მისი როლი RBAC-ისთვის
                $query = "SELECT r.role_name FROM roles r 
                          JOIN user_roles ur ON r.id = ur.role_id 
                          WHERE ur.user_id = :user_id";
                $stmt = $this->db->prepare($query);
                $stmt->execute(['user_id' => $user['id']]);
                $role = $stmt->fetch();

               $_SESSION['user_role'] = $role ? $role['role_name'] : 'User';

                Logger::log("წარმატებული ავტორიზაცია (Login)", $email);
                return true;
            }

            Logger::log("ავტორიზაციის წარუმატებელი მცდელობა (არასწორი მონაცემები)", $email);
            return "არასწორი ელ.ფოსტა ან პაროლი!";

        } catch (PDOException $e) {
            Logger::log("ავტორიზაციის შეცდომა: " . $e->getMessage(), $email);
            return "სისტემური შეცდომა ავტორიზაციისას.";
        }
    }
    // 3. ყველა მომხმარებლის ამოღება როლებთან ერთად (ადმინ პანელისთვის)
    public function getAllUsers() {
        try {
            $query = "SELECT u.id, u.email, u.created_at, r.role_name 
                      FROM users u
                      JOIN user_roles ur ON u.id = ur.user_id
                      JOIN roles r ON ur.role_id = r.id
                      ORDER BY u.id ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // 4. მომხმარებლის წაშლა ბაზიდან (ადმინის მიერ)
    public function deleteUser($user_id) {
        try {
            // უსაფრთხოება: ადმინმა საკუთარი თავი რომ არ წაშალოს შეცდომით
            if ($user_id == $_SESSION['user_id']) {
                return "თქვენ არ შეგიძლიათ საკუთარი თავის წაშლა!";
            }

            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $user_id]);

            Logger::log("ადმინისტრატორმა წაშალა მომხმარებელი ID: {$user_id}", $_SESSION['user_email']);
            return true;
        } catch (PDOException $e) {
            return "სისტემური შეცდომა მომხმარებლის წაშლისას.";
        }
    }
}