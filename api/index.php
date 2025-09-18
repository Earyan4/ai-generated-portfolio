<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'config/database.php';
require_once 'controllers/UserController.php';
require_once 'controllers/PortfolioController.php';
require_once 'models/UserMongo.php';

$database = new Database();
$db = $database->getConnection();

// Try Mongo connection (optional). Controllers still use PDO-backed models by default.
$mongo = new MongoConnection();
$mongoDb = $mongo->getDatabase();

$userController = new UserController($db, $mongoDb);
$portfolioController = new PortfolioController($db);

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/api/', '', $path);

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input)) {
    $input = $_POST;
}

// Route requests
switch ($path) {
    case 'register':
        if ($method === 'POST') {
            $result = $userController->register($input);
            echo json_encode($result);
        }
        break;

    case 'login':
        if ($method === 'POST') {
            $result = $userController->login($input['email'], $input['password']);
            echo json_encode($result);
        }
        break;

    case 'profile':
        if ($method === 'GET') {
            $userId = $_GET['id'] ?? null;
            if ($userId) {
                $result = $userController->getProfile($userId);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'User ID required']);
            }
        } elseif ($method === 'PUT') {
            $userId = $_GET['id'] ?? null;
            if ($userId) {
                $result = $userController->updateProfile($userId, $input);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'User ID required']);
            }
        }
        break;

    case 'save-profile':
        if ($method === 'POST') {
            $userId = $input['user_id'] ?? null;
            if ($userId) {
                $result = $userController->saveCompleteProfile($userId, $input);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'User ID required']);
            }
        }
        break;

    case 'generate-portfolio':
        if ($method === 'POST') {
            $userId = $input['user_id'] ?? null;
            $template = $input['template'] ?? 'default';
            if ($userId) {
                $result = $portfolioController->generatePortfolio($userId, $template);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'User ID required']);
            }
        }
        break;

    case 'get-portfolio':
        if ($method === 'GET') {
            $userId = $_GET['id'] ?? null;
            if ($userId) {
                $result = $portfolioController->getPortfolio($userId);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'User ID required']);
            }
        }
        break;

    case 'templates':
        if ($method === 'GET') {
            $result = $portfolioController->getTemplates();
            echo json_encode($result);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}
?>
