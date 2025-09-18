<?php
/**
 * Portfolio System Deployment Script
 * This script helps set up the portfolio system on a web server
 */

// Check if we're running from command line or web
$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    echo "<h1>Portfolio System Deployment</h1>";
    echo "<pre>";
}

echo "🚀 Starting Portfolio System Deployment...\n\n";

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'portfolio_system',
    'username' => 'root',
    'password' => ''
];

echo "📊 Setting up database...\n";

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host={$dbConfig['host']}", $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$dbConfig['dbname']}");
    echo "✅ Database '{$dbConfig['dbname']}' created/verified\n";
    
    // Use the database
    $pdo->exec("USE {$dbConfig['dbname']}");
    
    // Read and execute schema
    $schema = file_get_contents('api/database/schema.sql');
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ Database schema created successfully\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n📁 Setting up file permissions...\n";

// Create uploads directory
$uploadsDir = 'uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
    echo "✅ Created uploads directory\n";
} else {
    echo "✅ Uploads directory already exists\n";
}

// Set permissions
$directories = ['api', 'uploads'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        echo "✅ Set permissions for $dir\n";
    }
}

echo "\n🔧 Updating configuration...\n";

// Update database configuration if needed
$configFile = 'api/config/database.php';
if (file_exists($configFile)) {
    echo "✅ Database configuration file exists\n";
} else {
    echo "❌ Database configuration file not found\n";
}

echo "\n🌐 Setting up web server configuration...\n";

// Check if .htaccess exists
if (file_exists('.htaccess')) {
    echo "✅ .htaccess file exists\n";
} else {
    echo "❌ .htaccess file not found\n";
}

echo "\n📋 Deployment Summary:\n";
echo "========================\n";
echo "✅ Database: {$dbConfig['dbname']}\n";
echo "✅ API Endpoint: /api/\n";
echo "✅ Main Pages:\n";
echo "   - Portfolio Builder: premium-form.html\n";
echo "   - Portfolio Viewer: portfolio-viewer.html\n";
echo "   - Alternative Builder: portfolio-builder.html\n";
echo "✅ Upload Directory: /uploads/\n";

echo "\n🎉 Deployment completed successfully!\n";
echo "\nNext steps:\n";
echo "1. Update database credentials in api/config/database.php if needed\n";
echo "2. Test the system by visiting premium-form.html\n";
echo "3. Create your first portfolio!\n";

if (!$isCli) {
    echo "</pre>";
    echo "<p><a href='premium-form.html'>Go to Portfolio Builder</a></p>";
    echo "<p><a href='portfolio-viewer.html'>Go to Portfolio Viewer</a></p>";
}
?>

