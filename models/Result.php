<?php
/**
 * Result Model Class
 * Handles test results, scoring, and weak topic tracking
 */

class Result {
    private $connection;
    private $results_table = 'results';
    private $test_answers_table = 'test_answers';
    private $weak_topics_table = 'weak_topics';
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    /**
     * Save test result
     */
    public function saveResult($user_id, $technology, $total_questions, $correct_answers, $wrong_answers, $test_duration_seconds, $answers_data) {
        // Calculate accuracy percentage
        $accuracy = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100, 2) : 0;
        
        // Calculate score (out of 100)
        $score = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100) : 0;
        
        // Start transaction
        $this->connection->begin_transaction();
        
        try {
            // Insert result
            $query = "INSERT INTO $this->results_table (user_id, technology, total_questions, correct_answers, wrong_answers, accuracy_percentage, score, test_duration_seconds) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                throw new Exception("Database error: " . $this->connection->error);
            }
            
            $stmt->bind_param("isiiiiii", $user_id, $technology, $total_questions, $correct_answers, $wrong_answers, $accuracy, $score, $test_duration_seconds);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to save result: " . $this->connection->error);
            }
            
            $result_id = $this->connection->insert_id;
            $stmt->close();
            
            // Insert individual answers and track weak topics
            foreach ($answers_data as $answer) {
                $this->saveAnswer($result_id, $answer['question_id'], $answer['user_answer'], $answer['is_correct'], $answer['topic'], $user_id, $technology);
            }
            
            // Commit transaction
            $this->connection->commit();
            
            return array('success' => true, 'message' => 'Result saved successfully', 'result_id' => $result_id, 'score' => $score, 'accuracy' => $accuracy);
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->connection->rollback();
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    /**
     * Save individual answer and update weak topics
     */
    private function saveAnswer($result_id, $question_id, $user_answer, $is_correct, $topic, $user_id, $technology) {
        // Insert answer record
        $query = "INSERT INTO $this->test_answers_table (result_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error: " . $this->connection->error);
        }
        
        $is_correct_int = $is_correct ? 1 : 0;
        $stmt->bind_param("iisi", $result_id, $question_id, $user_answer, $is_correct_int);
        $stmt->execute();
        $stmt->close();
        
        // Update weak topics
        $this->updateWeakTopic($user_id, $technology, $topic, $is_correct);
    }
    
    /**
     * Update weak topic tracking
     */
    private function updateWeakTopic($user_id, $technology, $topic, $is_correct) {
        if ($is_correct) {
            // Increment correct count
            $query = "INSERT INTO $this->weak_topics_table (user_id, technology, topic, correct_count) 
                      VALUES (?, ?, ?, 1) 
                      ON DUPLICATE KEY UPDATE correct_count = correct_count + 1, last_attempted = CURRENT_TIMESTAMP";
        } else {
            // Increment wrong count
            $query = "INSERT INTO $this->weak_topics_table (user_id, technology, topic, wrong_count) 
                      VALUES (?, ?, ?, 1) 
                      ON DUPLICATE KEY UPDATE wrong_count = wrong_count + 1, last_attempted = CURRENT_TIMESTAMP";
        }
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error: " . $this->connection->error);
        }
        
        $stmt->bind_param("iss", $user_id, $technology, $topic);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Get all results for a user
     */
    public function getUserResults($user_id, $technology = null) {
        if ($technology) {
            $query = "SELECT * FROM $this->results_table WHERE user_id = ? AND technology = ? ORDER BY test_date DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("is", $user_id, $technology);
        } else {
            $query = "SELECT * FROM $this->results_table WHERE user_id = ? ORDER BY test_date DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("i", $user_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $results = array();
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        
        $stmt->close();
        return $results;
    }
    
    /**
     * Get result by ID
     */
    public function getResultById($id, $user_id) {
        $query = "SELECT * FROM $this->results_table WHERE id = ? AND user_id = ?";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $res = $result->fetch_assoc();
            $stmt->close();
            return $res;
        }
        
        $stmt->close();
        return false;
    }
    
    /**
     * Get test answers for a result
     */
    public function getTestAnswers($result_id) {
        $query = "SELECT ta.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_answer 
                  FROM $this->test_answers_table ta 
                  JOIN questions q ON ta.question_id = q.id 
                  WHERE ta.result_id = ?";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $result_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $answers = array();
        while ($row = $result->fetch_assoc()) {
            $answers[] = $row;
        }
        
        $stmt->close();
        return $answers;
    }
    
    /**
     * Get dashboard statistics for user
     */
    public function getDashboardStats($user_id) {
        $stats = array();
        
        // Total tests taken
        $query = "SELECT COUNT(*) as total_tests FROM $this->results_table WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total_tests'] = $row['total_tests'];
        $stmt->close();
        
        // Average accuracy
        $query = "SELECT AVG(accuracy_percentage) as average_accuracy FROM $this->results_table WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['average_accuracy'] = $row['average_accuracy'] ? round($row['average_accuracy'], 2) : 0;
        $stmt->close();
        
        // Best score
        $query = "SELECT MAX(score) as best_score FROM $this->results_table WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['best_score'] = $row['best_score'] ? $row['best_score'] : 0;
        $stmt->close();
        
        // Average score
        $query = "SELECT AVG(score) as average_score FROM $this->results_table WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['average_score'] = $row['average_score'] ? round($row['average_score'], 2) : 0;
        $stmt->close();
        
        return $stats;
    }
    
    /**
     * Get performance data for chart
     */
    public function getPerformanceData($user_id) {
        $query = "SELECT test_date, accuracy_percentage, score, technology FROM $this->results_table WHERE user_id = ? ORDER BY test_date ASC LIMIT 20";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }
    
    /**
     * Get weak topics for user
     */
    public function getWeakTopics($user_id, $technology = null) {
        if ($technology) {
            $query = "SELECT * FROM $this->weak_topics_table WHERE user_id = ? AND technology = ? AND wrong_count >= 3 ORDER BY wrong_count DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("is", $user_id, $technology);
        } else {
            $query = "SELECT * FROM $this->weak_topics_table WHERE user_id = ? AND wrong_count >= 3 ORDER BY wrong_count DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("i", $user_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $topics = array();
        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
        
        $stmt->close();
        return $topics;
    }
    
    /**
     * Get all user results (for admin)
     */
    public function getAllResults($limit = 100, $offset = 0) {
        $query = "SELECT r.*, u.username, u.full_name FROM $this->results_table r 
                  JOIN users u ON r.user_id = u.id 
                  ORDER BY r.test_date DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $results = array();
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        
        $stmt->close();
        return $results;
    }
}
?>
