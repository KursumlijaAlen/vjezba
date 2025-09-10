<?php

class ExamDao
{

  private $conn;

  /**
   * constructor of dao class
   */
  public function __construct()
{
  try {
    /** TODO
     * List parameters such as servername, username, password, schema. Make sure to use appropriate port
     */
    $servername = "db1.ibu.edu.ba";
    $schema     = "webfinalmakeup_db_1507";
    $port       = "3306";
    $username   = "webfinalmup_db_user2";
    $password   = "webFinMup1507";

    /** TODO
     * Create new connection
     */
    $this->conn = new PDO(
      "mysql:host={$servername};dbname={$schema};port={$port};charset=utf8mb4",
      $username,
      $password,
      [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
      ]
    );

    echo "Connected successfully";
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}

         $this->conn = new PDO(
                    "mysql:host=" . "db1.ibu.edu.ba" . ";dbname=" . "webfinalmakeup_db_1507" . ";port=" . "3306",
                    "webfinalmup_db_user2",
                    "webFinMup1507",

                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );



      echo "Connected successfully";
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }
public function getUserByEmail($email) {
    $sql = "SELECT id, email, password FROM users WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch();
}

public function login($entity) {
    $user = $this->getUserByEmail($entity['email']);
    if (!$user) {
        return ['success' => false, 'error' => 'Invalid username or password.'];
    }

    if ($entity['password'] !== $user['password']) {
        return ['success' => false, 'error' => 'Invalid username or password.'];
    }

    unset($user['password']);

    $token = base64_encode(json_encode([
        'id' => $user['id'],  // Use 'id' not 'user_id'
        'email' => $user['email'],
        'exp' => time() + (60 * 60 * 24)
    ]));

    return [
        'success' => true, 
        'message' => 'Login successful',
        'jwt' => $token,
        'user' => $user
    ];
}


public function film_performance_report() {
    $sql = "SELECT c.category_id, c.name, COUNT(f.film_id) as total_number_of_movies 
            FROM category c
            JOIN film_category fc ON c.category_id = fc.category_id
            JOIN film f ON fc.film_id = f.film_id
            GROUP BY c.category_id, c.name
            ORDER BY c.category_id";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}


  public function delete_film($film_id) {
    $sql = "DELETE FROM film WHERE film_id = :film_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':film_id', $film_id, PDO::PARAM_INT);
    $stmt->execute();
    return ['message' => 'Film deleted successfully'];
}

public function edit_film($film_id, $data) {
    if (empty($data)) {
        throw new Exception('Update data array cannot be empty.');
    }

    $setParts = [];
    $params = [];

    foreach ($data as $column => $value) {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            continue;
        }
        $placeholder = ":" . $column;
        $setParts[] = "`{$column}` = {$placeholder}";
        $params[$placeholder] = $value;
    }

    if (empty($setParts)) {
        throw new Exception('No valid columns provided for update.');
    }

    $setClause = implode(', ', $setParts);
    $sql = "UPDATE `film` SET {$setClause} WHERE `film_id` = :film_id";
    $params[':film_id'] = $film_id;

    try {
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute($params);
        
        if ($success) {
            // Return updated film
            $sql2 = "SELECT * FROM film WHERE film_id = :film_id";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(':film_id', $film_id, PDO::PARAM_INT);
            $stmt2->execute();
            return $stmt2->fetch();
        }
        return false;
    } catch (PDOException $e) {
        error_log("Database update error: " . $e->getMessage());
        return false;
    }
}

public function get_customers_report() {
    $sql = "SELECT c.customer_id, 
                   CONCAT(c.first_name, ' ', c.last_name) as customer_full_name,
                   SUM(p.amount) as total_amount
            FROM customer c
            JOIN rental r ON c.customer_id = r.customer_id
            JOIN payment p ON r.rental_id = p.rental_id
            GROUP BY c.customer_id, c.first_name, c.last_name
            ORDER BY total_amount DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}

public function get_customer_rental_details($customer_id) {
    $sql = "SELECT r.rental_date, f.title as film_title, p.amount as payment_amount
            FROM rental r
            JOIN payment p ON r.rental_id = p.rental_id
            JOIN inventory i ON r.inventory_id = i.inventory_id
            JOIN film f ON i.film_id = f.film_id
            WHERE r.customer_id = :customer_id
            ORDER BY r.rental_date DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}
}
