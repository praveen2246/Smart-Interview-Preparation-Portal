<?php
/**
 * Question Model Class
 * Handles question retrieval, filtering, and management
 */

class Question {
    private $connection;
    private $table = 'questions';
    
    public $id;
    public $technology;
    public $topic;
    public $question_text;
    public $option_a;
    public $option_b;
    public $option_c;
    public $option_d;
    public $correct_answer;
    public $difficulty;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    /**
     * Get random questions for test
     * Returns 10 random questions for specified technology and optional difficulty
     */
    public function getRandomQuestions($technology, $difficulty = null, $limit = 10) {
        if ($difficulty) {
            $query = "SELECT id, question_text, option_a, option_b, option_c, option_d, topic, difficulty, technology FROM $this->table 
                      WHERE technology = ? AND difficulty = ? 
                      ORDER BY RAND() LIMIT ?";
            
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                return false;
            }
            
            $stmt->bind_param("ssi", $technology, $difficulty, $limit);
        } else {
            $query = "SELECT id, question_text, option_a, option_b, option_c, option_d, topic, difficulty, technology FROM $this->table 
                      WHERE technology = ? 
                      ORDER BY RAND() LIMIT ?";
            
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                return false;
            }
            
            $stmt->bind_param("si", $technology, $limit);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $questions = array();
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        
        $stmt->close();
        return $questions;
    }
    
    /**
     * Get question by ID with correct answer hidden
     */
    public function getQuestionById($id) {
        $query = "SELECT id, question_text, option_a, option_b, option_c, option_d, topic, difficulty FROM $this->table WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $question = $result->fetch_assoc();
            $stmt->close();
            return $question;
        }
        
        $stmt->close();
        return false;
    }
    
    /**
     * Get correct answer for a question
     */
    public function getCorrectAnswer($id) {
        $query = "SELECT correct_answer, topic FROM $this->table WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $answer = $result->fetch_assoc();
            $stmt->close();
            return $answer;
        }
        
        $stmt->close();
        return false;
    }
    
    /**
     * Get all questions (for admin)
     */
    public function getAllQuestions($technology = null, $difficulty = null) {
        $query = "SELECT * FROM $this->table WHERE 1=1";
        
        if ($technology) {
            $query .= " AND technology = ?";
        }
        
        if ($difficulty) {
            $query .= " AND difficulty = ?";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            return false;
        }
        
        if ($technology && $difficulty) {
            $stmt->bind_param("ss", $technology, $difficulty);
        } elseif ($technology) {
            $stmt->bind_param("s", $technology);
        } elseif ($difficulty) {
            $stmt->bind_param("s", $difficulty);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $questions = array();
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        
        $stmt->close();
        return $questions;
    }
    
    /**
     * Add a new question (admin function)
     */
    public function addQuestion($technology, $topic, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty) {
        // Validate inputs
        if (!in_array($technology, ['PHP', 'Java', 'React'])) {
            return array('success' => false, 'message' => 'Invalid technology');
        }
        
        if (!in_array($correct_answer, ['A', 'B', 'C', 'D'])) {
            return array('success' => false, 'message' => 'Invalid correct answer');
        }
        
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            return array('success' => false, 'message' => 'Invalid difficulty level');
        }
        
        $query = "INSERT INTO $this->table (technology, topic, question_text, option_a, option_b, option_c, option_d, correct_answer, difficulty) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error: ' . $this->connection->error);
        }
        
        $stmt->bind_param("sssssssss", $technology, $topic, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty);
        
        if ($stmt->execute()) {
            $stmt->close();
            return array('success' => true, 'message' => 'Question added successfully');
        } else {
            $stmt->close();
            return array('success' => false, 'message' => 'Failed to add question: ' . $this->connection->error);
        }
    }
    
    /**
     * Update a question (admin function)
     */
    public function updateQuestion($id, $technology, $topic, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty) {
        if (!in_array($technology, ['PHP', 'Java', 'React']) || !in_array($difficulty, ['easy', 'medium', 'hard'])) {
            return array('success' => false, 'message' => 'Invalid input data');
        }
        
        $query = "UPDATE $this->table SET technology = ?, topic = ?, question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, difficulty = ? WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error');
        }
        
        $stmt->bind_param("sssssssssi", $technology, $topic, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return array('success' => true, 'message' => 'Question updated successfully');
        } else {
            $stmt->close();
            return array('success' => false, 'message' => 'Failed to update question');
        }
    }
    
    /**
     * Delete a question (admin function)
     */
    public function deleteQuestion($id) {
        $query = "DELETE FROM $this->table WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error');
        }
        
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return array('success' => true, 'message' => 'Question deleted successfully');
        } else {
            $stmt->close();
            return array('success' => false, 'message' => 'Failed to delete question');
        }
    }
    
    /**
     * Get question count for specific technology
     */
    public function getQuestionCount($technology, $difficulty = null) {
        if ($difficulty) {
            $query = "SELECT COUNT(*) as count FROM $this->table WHERE technology = ? AND difficulty = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ss", $technology, $difficulty);
        } else {
            $query = "SELECT COUNT(*) as count FROM $this->table WHERE technology = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("s", $technology);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'];
    }
}
?>
