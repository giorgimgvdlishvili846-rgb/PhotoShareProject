<?php
// აქაც პირდაპირ შემოგვაქვს, რადგან ერთ პაპკაშია
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Logger.php';

class Post {
    private $db;
// ... აქედან კოდი გრძელდება უცვლელად ...

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // 1. ფოტოს ატვირთვა და ბაზაში შენახვა (Create)
   // 1. ფოტოს ატვირთვა და ბაზაში შენახვა (Create)
    public function create($user_id, $title, $file) {
        try {
            // აბსოლუტური გზა პროექტის მთავარ საქაღალდემდე
            $target_dir = dirname(__DIR__) . "/uploads/";
            
            // თუ uploads საქაღალდე არ არსებობს, ავტომატურად შევქმნათ და მივცეთ ჩაწერის უფლება
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0775, true);
            }
            
            // უსაფრთხოება: ფაილის გაფართოების შემოწმება
            $image_file_type = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($image_file_type, $allowed_types)) {
                return "შეცდომა: ნებადართულია მხოლოდ JPG, JPEG, PNG და GIF ფაილები.";
            }

            // უსაფრთხოება: უნიკალური სახელის გენერირება
            $unique_filename = uniqid() . "." . $image_file_type;
            $target_file = $target_dir . $unique_filename;

            // ფიზიკურად გადავიტანოთ ფაილი uploads საქაღალდეში
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                
                // ჩავწეროთ მონაცემები ბაზაში
                $query = "INSERT INTO posts (user_id, title, image) VALUES (:user_id, :title, :image)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'user_id' => $user_id,
                    'title' => htmlspecialchars($title),
                    'image' => $unique_filename
                ]);

                Logger::log("ატვირთა ახალი ფოტო: {$unique_filename}", $_SESSION['user_email'] ?? 'სისტემა');
                return true;
            } else {
                return "ფაილის სერვერზე ატვირთვისას დაფიქსირდა შეცდომა. შეამოწმეთ საქაღალდის უფლებები.";
            }
        } catch (PDOException $e) {
            Logger::log("ფოტოს ბაზაში ჩაწერის შეცდომა: " . $e->getMessage());
            return "სისტემური შეცდომა ფოტოს შეცვლისას.";
        }
    }

    // 2. ყველა ფოტოს ამოღება ავტორის სახელით (Read / Select)
    public function getAllWithUsers() {
        try {
            $query = "SELECT p.*, u.email FROM posts p 
                      JOIN users u ON p.user_id = u.id 
                      ORDER BY p.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // 3. ფოტოს წაშლა (Delete - RBAC დაცვით)
    public function delete($post_id, $user_id, $user_role) {
        try {
            // ჯერ ვამოწმებთ ფოტოს არსებობას და მის მფლობელს
            $query = "SELECT * FROM posts WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $post_id]);
            $post = $stmt->fetch();

            if (!$post) {
                return "ფოტო ვერ მოიძებნა.";
            }

            // უსაფრთხოების კონტროლი (RBAC): წაშლა შეუძლია მხოლოდ ავტორს ან ადმინს
            if ($post['user_id'] == $user_id || $user_role === 'Admin') {
                
                // ფიზიკური ფაილის წაშლა სერვერიდან
                $file_path = __DIR__ . "/../uploads/" . $post['image'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                // ჩანაწერის წაშლა ბაზიდან
                $query = "DELETE FROM posts WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->execute(['id' => $post_id]);

                Logger::log("წაშალა ფოტო ID: {$post_id}", $_SESSION['user_email'] ?? 'სისტემა');
                return true;
            }

            return "წვდომა უარყოფილია! თქვენ არ გაქვთ ამ ფოტოს წაშლის უფლება.";
        } catch (PDOException $e) {
            return "სისტემური შეცდომა ფოტოს წაშლისას.";
        }
    }
}