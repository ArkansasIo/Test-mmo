<?php

class Forum {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
    }

    public function getTopics() {
        $stmt = $this->pdo->query("SELECT * FROM forum_topics ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPosts($topic_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM forum_posts WHERE topic_id = ? ORDER BY created_at ASC");
        $stmt->execute([$topic_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTopic($title, $player_id) {
        $stmt = $this->pdo->prepare("INSERT INTO forum_topics (title, player_id) VALUES (?, ?)");
        $stmt->execute([$title, $player_id]);
    }

    public function createPost($topic_id, $player_id, $content) {
        $stmt = $this->pdo->prepare("INSERT INTO forum_posts (topic_id, player_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$topic_id, $player_id, $content]);
    }
}
?>
