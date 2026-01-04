<?php
header('Content-Type: application/json');

// Database configuration
$dbHost = getenv('DB_HOST') ?: 'postgres';
$dbPort = getenv('DB_PORT') ?: '5432';
$dbName = getenv('DB_NAME') ?: 'contactsdb';
$dbUser = getenv('DB_USER') ?: 'webappuser';
$dbPass = getenv('DB_PASS') ?: 'webapppass';

// Connect to PostgreSQL
try {
    $dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        handlePost($pdo);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

// Handle GET requests (Read)
function handleGet($pdo) {
    try {
        if (isset($_GET['id'])) {
            // Get single contact
            $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = :id');
            $stmt->execute(['id' => $_GET['id']]);
            $result = $stmt->fetchAll();
        } else {
            // Get all contacts
            $stmt = $pdo->query('SELECT * FROM contacts ORDER BY id ASC');
            $result = $stmt->fetchAll();
        }
        echo json_encode($result);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Handle POST requests (Create, Update, Delete)
function handlePost($pdo) {
    $action = $_POST['action'] ?? 'create';

    try {
        switch ($action) {
            case 'create':
                createContact($pdo);
                break;
            case 'update':
                updateContact($pdo);
                break;
            case 'delete':
                deleteContact($pdo);
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Create new contact
function createContact($pdo) {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'First name, last name, and email are required']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }

    $stmt = $pdo->prepare('
        INSERT INTO contacts (first_name, last_name, email, phone)
        VALUES (:firstName, :lastName, :email, :phone)
    ');

    $stmt->execute([
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'phone' => $phone ?: null
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Contact created successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

// Update existing contact
function updateContact($pdo) {
    $id = $_POST['id'] ?? 0;
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($id) || empty($firstName) || empty($lastName) || empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID, first name, last name, and email are required']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }

    $stmt = $pdo->prepare('
        UPDATE contacts
        SET first_name = :firstName,
            last_name = :lastName,
            email = :email,
            phone = :phone,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ');

    $stmt->execute([
        'id' => $id,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'phone' => $phone ?: null
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Contact updated successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Contact not found']);
    }
}

// Delete contact
function deleteContact($pdo) {
    $id = $_POST['id'] ?? 0;

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID is required']);
        return;
    }

    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = :id');
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Contact deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Contact not found']);
    }
}
