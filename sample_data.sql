-- Sample Data for SIPP - Smart Interview Preparation Portal
-- Insert test admin, sample questions, and initial data

-- Create Admin Account
-- Username: admin, Password: admin123
INSERT INTO `admin` (`username`, `email`, `password`, `full_name`) VALUES 
('admin', 'admin@sipp.local', '$2y$10$YJg4/rDGkDfcRLfWKyQKkO5p7Rb5z4BmH6yRe9r9iOgXOCR.c2K8.', 'System Administrator');

-- Sample PHP Questions
INSERT INTO `questions` (`technology`, `topic`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `difficulty`) VALUES
('PHP', 'Arrays', 'Which function is used to count the number of elements in an array?', 'count()', 'sizeof()', 'Both A and B', 'length()', 'C', 'easy'),
('PHP', 'Variables', 'What is the correct way to declare a variable in PHP?', '$variable = value', 'variable = value', '$variable: value', 'var variable = value', 'A', 'easy'),
('PHP', 'Strings', 'Which function is used to find the length of a string?', 'strlen()', 'str_length()', 'length()', 'string_length()', 'A', 'easy'),
('PHP', 'OOP', 'What is the correct syntax for creating a class in PHP?', 'class MyClass {}', 'class MyClass;', 'Class MyClass {}', 'class MyClass []', 'A', 'medium'),
('PHP', 'OOP', 'Which keyword is used to create an object of a class?', 'new', 'create', 'instance', 'object', 'A', 'medium'),
('PHP', 'Functions', 'What is the purpose of the return statement in a function?', 'To exit the function and return a value', 'To continue execution', 'To skip a loop', 'To declare variables', 'A', 'easy'),
('PHP', 'Sessions', 'Which function is used to start a session in PHP?', 'session_start()', 'start_session()', 'init_session()', 'create_session()', 'A', 'medium'),
('PHP', 'Security', 'What is the purpose of prepared statements in database queries?', 'To prevent SQL injection attacks', 'To speed up queries', 'To reduce code length', 'To improve readability', 'A', 'hard'),
('PHP', 'Error Handling', 'Which function is used to trigger an error in PHP?', 'trigger_error()', 'throw_error()', 'error()', 'raise_error()', 'A', 'medium'),
('PHP', 'File Handling', 'Which function is used to read a file in PHP?', 'file_get_contents()', 'read_file()', 'get_file()', 'file_read()', 'A', 'easy');

-- Sample Java Questions
INSERT INTO `questions` (`technology`, `topic`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `difficulty`) VALUES
('Java', 'Variables', 'What is the default value of an integer variable in Java?', '0', 'null', 'undefined', 'No default value', 'A', 'easy'),
('Java', 'OOP', 'Which keyword is used to inherit a class in Java?', 'extends', 'inherits', 'implements', 'super', 'A', 'medium'),
('Java', 'Collections', 'Which interface extends Collection interface?', 'List', 'Set', 'Queue', 'All of the above', 'D', 'medium'),
('Java', 'Exceptions', 'Which exception is thrown when an array is accessed with an invalid index?', 'ArrayIndexOutOfBoundsException', 'ArrayException', 'IndexOutOfBoundsException', 'InvalidArrayException', 'A', 'medium'),
('Java', 'Threads', 'How many threads can be created in a Java program?', 'Unlimited', 'Only one', 'Only five', 'Depends on system memory', 'A', 'hard'),
('Java', 'Strings', 'What is the main difference between String and StringBuffer?', 'String is immutable, StringBuffer is mutable', 'String is mutable, StringBuffer is immutable', 'Both are immutable', 'Both are mutable', 'A', 'medium'),
('Java', 'Access Modifiers', 'What is the default access modifier in Java?', 'Package-private', 'Public', 'Private', 'Protected', 'A', 'medium'),
('Java', 'Keywords', 'What does the static keyword do?', 'Defines a class member, not an instance member', 'Makes a variable constant', 'Prevents instantiation', 'Makes a method abstract', 'A', 'medium'),
('Java', 'Constructors', 'Can a constructor return a value?', 'No, constructor returns nothing but creates instance', 'Yes, it returns an object', 'Yes, it returns a boolean', 'Only if declared as non-void', 'A', 'medium'),
('Java', 'Interfaces', 'Can a class implement multiple interfaces?', 'Yes', 'No', 'Only two interfaces', 'Depends on the JVM', 'A', 'easy');

-- Sample React Questions
INSERT INTO `questions` (`technology`, `topic`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `difficulty`) VALUES
('React', 'Components', 'What are the two types of components in React?', 'Functional and Class components', 'Stateful and Stateless', 'Container and Presentational', 'React and ReactDOM', 'A', 'easy'),
('React', 'JSX', 'What is JSX?', 'A JavaScript extension that allows HTML-like syntax in JavaScript', 'A CSS preprocessor', 'A templating language', 'A server-side framework', 'A', 'easy'),
('React', 'State', 'How do you update state in a functional component?', 'Using the useState hook', 'Using this.setState()', 'Direct assignment', 'Using the useEffect hook', 'A', 'medium'),
('React', 'Props', 'Props are passed from...', 'Parent to child component', 'Child to parent component', 'Sibling components', 'Globally', 'A', 'easy'),
('React', 'Hooks', 'What does the useEffect hook do?', 'Allows you to perform side effects in functional components', 'Manages component state', 'Handles component mounting only', 'Prevents re-renders', 'A', 'medium'),
('React', 'Keys', 'Why are keys important in lists?', 'To help React identify which items have changed', 'To improve component performance', 'To prevent errors', 'To sort the list', 'A', 'medium'),
('React', 'Events', 'How do you bind an event handler in a functional component?', 'Pass the function reference directly', 'Use the bind method', 'Use arrow functions in the render method', 'Use the on prefix', 'A', 'easy'),
('React', 'Context', 'What does React.createContext() do?', 'Creates a Context object to share data without prop drilling', 'Creates a new component', 'Creates a new file', 'Creates a new state', 'A', 'hard'),
('React', 'Routing', 'Which library is commonly used for routing in React?', 'React Router', 'React Navigation', 'Redux Router', 'React Nav', 'A', 'easy'),
('React', 'State Management', 'What is Redux used for?', 'Predictable state management across the application', 'Server-side rendering', 'CSS styling', 'API calls', 'A', 'medium');

-- Sample Test Users (for demonstration)
-- Password for both: password123
INSERT INTO `users` (`username`, `email`, `password`, `full_name`) VALUES 
('john_dev', 'john@example.com', '$2y$10$lK6MfBRmL0.3e7nKJVZ8.uFmHvEF8bHAMc.8yZt5v2P/H5V9z9aD6', 'John Developer'),
('sarah_coder', 'sarah@example.com', '$2y$10$lK6MfBRmL0.3e7nKJVZ8.uFmHvEF8bHAMc.8yZt5v2P/H5V9z9aD6', 'Sarah Coder');

-- Sample Results (for demonstration)
-- John has taken a PHP test
INSERT INTO `results` (`user_id`, `technology`, `total_questions`, `correct_answers`, `wrong_answers`, `accuracy_percentage`, `score`, `test_duration_seconds`, `test_date`) VALUES 
(1, 'PHP', 10, 8, 2, 80.00, 80, 480, NOW() - INTERVAL 7 DAY),
(1, 'PHP', 10, 9, 1, 90.00, 90, 520, NOW() - INTERVAL 5 DAY),
(1, 'Java', 10, 7, 3, 70.00, 70, 600, NOW() - INTERVAL 3 DAY),
(2, 'React', 10, 6, 4, 60.00, 60, 550, NOW() - INTERVAL 6 DAY),
(2, 'React', 10, 8, 2, 80.00, 80, 490, NOW() - INTERVAL 2 DAY);

-- Sample Weak Topics for John
INSERT INTO `weak_topics` (`user_id`, `technology`, `topic`, `wrong_count`, `correct_count`, `last_attempted`) VALUES 
(1, 'PHP', 'OOP', 2, 4, NOW() - INTERVAL 5 DAY),
(1, 'Java', 'Threads', 3, 1, NOW() - INTERVAL 3 DAY),
(2, 'React', 'Context', 4, 1, NOW() - INTERVAL 2 DAY);
