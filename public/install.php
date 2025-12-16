<?php
/**
 * MLJNET RADIUS - Web Installer for cPanel
 * File ini digunakan untuk memeriksa dan menginstall aplikasi di hosting cPanel
 */

// Prevent direct access if already installed
if (file_exists('../.env') && !isset($_GET['force'])) {
    die('Aplikasi sudah terinstall. Hapus file ini untuk keamanan.');
}

$step = isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : 1);
$errors = [];
$success = [];

// Function to check requirements
function checkRequirements() {
    $requirements = [
        'PHP Version >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'MBString Extension' => extension_loaded('mbstring'),
        'OpenSSL Extension' => extension_loaded('openssl'),
        'Tokenizer Extension' => extension_loaded('tokenizer'),
        'XML Extension' => extension_loaded('xml'),
        'CType Extension' => extension_loaded('ctype'),
        'JSON Extension' => extension_loaded('json'),
        'BCMath Extension' => extension_loaded('bcmath'),
        'FileInfo Extension' => extension_loaded('fileinfo'),
        'ZIP Extension' => extension_loaded('zip'),
    ];

    $filePermissions = [
        '../storage/' => is_writable('../storage/'),
        '../bootstrap/cache/' => is_writable('../bootstrap/cache/'),
        '../.env' => !file_exists('../.env') || is_writable('../.env'),
    ];

    return [$requirements, $filePermissions];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        // Check requirements
        list($requirements, $filePermissions) = checkRequirements();

        $allRequirementsMet = !in_array(false, $requirements, true);
        $allPermissionsOk = !in_array(false, $filePermissions, true);

        if ($allRequirementsMet && $allPermissionsOk) {
            $step = 2;
            $success[] = 'Semua persyaratan terpenuhi. Lanjut ke konfigurasi database.';
        } else {
            $errors[] = 'Persyaratan belum terpenuhi. Periksa kembali.';
        }
    } elseif ($step == 2) {
        // Setup .env
        $dbHost = $_POST['db_host'] ?? 'localhost';
        $dbPort = $_POST['db_port'] ?? '3306';
        $dbName = $_POST['db_name'] ?? '';
        $dbUser = $_POST['db_user'] ?? '';
        $dbPass = $_POST['db_pass'] ?? '';
        $appUrl = $_POST['app_url'] ?? '';
        $adminEmail = $_POST['admin_email'] ?? '';
        $adminPass = $_POST['admin_pass'] ?? '';

        if (empty($dbName) || empty($dbUser) || empty($appUrl) || empty($adminEmail) || empty($adminPass)) {
            $errors[] = 'Semua field harus diisi.';
        } else {
            // Create .env file
            $envContent = file_get_contents('../.env.example');
            if (!$envContent) {
                $errors[] = 'File .env.example tidak ditemukan.';
            } else {
                $envContent = str_replace('APP_NAME=Laravel', 'APP_NAME="MLJ Net"', $envContent);
                $envContent = str_replace('APP_ENV=local', 'APP_ENV=production', $envContent);
                $envContent = str_replace('APP_DEBUG=true', 'APP_DEBUG=false', $envContent);
                $envContent = str_replace('APP_URL=http://localhost', 'APP_URL=' . $appUrl, $envContent);
                $envContent = str_replace('DB_HOST=127.0.0.1', 'DB_HOST=' . $dbHost, $envContent);
                $envContent = str_replace('DB_PORT=3306', 'DB_PORT=' . $dbPort, $envContent);
                $envContent = str_replace('DB_DATABASE=laravel', 'DB_DATABASE=' . $dbName, $envContent);
                $envContent = str_replace('DB_USERNAME=root', 'DB_USERNAME=' . $dbUser, $envContent);
                $envContent = str_replace('DB_PASSWORD=', 'DB_PASSWORD=' . $dbPass, $envContent);

                if (file_put_contents('../.env', $envContent)) {
                    $step = 3;
                    $success[] = '.env file berhasil dibuat.';
                } else {
                    $errors[] = 'Gagal membuat file .env. Periksa permissions.';
                }
            }
        }
    } elseif ($step == 3) {
        // Run migrations and seed
        if (function_exists('shell_exec')) {
            $output = shell_exec('cd ../ && php artisan key:generate 2>&1');
            if ($output) {
                $success[] = 'Application key generated.';
            }

            $output = shell_exec('cd ../ && php artisan migrate --seed 2>&1');
            if (strpos($output, 'Migrated') !== false) {
                $success[] = 'Database migrated and seeded.';
                $step = 4;
            } else {
                $errors[] = 'Migration failed: ' . $output;
            }

            $output = shell_exec('cd ../ && php artisan storage:link 2>&1');
            if ($output) {
                $success[] = 'Storage link created.';
            }
        } else {
            $errors[] = 'shell_exec disabled. Jalankan command manual: php artisan key:generate, php artisan migrate --seed, php artisan storage:link';
            $step = 4;
        }
    }
}

list($requirements, $filePermissions) = checkRequirements();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MLJNET RADIUS - Web Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">MLJNET RADIUS - Web Installer</h1>

                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <ul>
                            <?php foreach ($success as $msg): ?>
                                <li><?php echo htmlspecialchars($msg); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($step == 1): ?>
                    <h2 class="text-2xl font-semibold mb-4">Step 1: Check Requirements</h2>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">PHP Extensions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <?php foreach ($requirements as $req => $status): ?>
                                <div class="flex items-center">
                                    <span class="w-4 h-4 rounded-full mr-2 <?php echo $status ? 'bg-green-500' : 'bg-red-500'; ?>"></span>
                                    <span class="<?php echo $status ? 'text-green-700' : 'text-red-700'; ?>"><?php echo $req; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">File Permissions</h3>
                        <div class="space-y-2">
                            <?php foreach ($filePermissions as $file => $writable): ?>
                                <div class="flex items-center">
                                    <span class="w-4 h-4 rounded-full mr-2 <?php echo $writable ? 'bg-green-500' : 'bg-red-500'; ?>"></span>
                                    <span class="<?php echo $writable ? 'text-green-700' : 'text-red-700'; ?>"><?php echo $file; ?> (<?php echo $writable ? 'Writable' : 'Not Writable'; ?>)</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <form method="post">
                        <input type="hidden" name="step" value="1">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Check & Continue
                        </button>
                    </form>

                <?php elseif ($step == 2): ?>
                    <h2 class="text-2xl font-semibold mb-4">Step 2: Database Configuration</h2>

                    <form method="post" class="space-y-4">
                        <input type="hidden" name="step" value="2">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Application URL</label>
                            <input type="url" name="app_url" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-sm text-gray-500">Contoh: https://yourdomain.com</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Database Host</label>
                                <input type="text" name="db_host" value="localhost" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Database Port</label>
                                <input type="number" name="db_port" value="3306" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Database Name</label>
                            <input type="text" name="db_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Database Username</label>
                                <input type="text" name="db_user" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Database Password</label>
                                <input type="password" name="db_pass" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Admin Email</label>
                                <input type="email" name="admin_email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Admin Password</label>
                                <input type="password" name="admin_pass" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Setup Database
                        </button>
                    </form>

                <?php elseif ($step == 3): ?>
                    <h2 class="text-2xl font-semibold mb-4">Step 3: Install Database</h2>
                    <p class="mb-4">Klik tombol di bawah untuk menjalankan migrasi database dan seeding data awal.</p>

                    <form method="post">
                        <input type="hidden" name="step" value="3">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Install Database
                        </button>
                    </form>

                <?php elseif ($step == 4): ?>
                    <h2 class="text-2xl font-semibold mb-4 text-green-600">Installation Complete!</h2>
                    <div class="bg-green-50 border border-green-200 rounded p-4 mb-4">
                        <p class="text-green-800">MLJNET RADIUS berhasil diinstall!</p>
                        <p class="text-green-800 mt-2">Langkah selanjutnya:</p>
                        <ul class="list-disc list-inside text-green-700 mt-2">
                            <li>Hapus file <code>install.php</code> ini untuk keamanan</li>
                            <li>Login ke admin panel di <code><?php echo htmlspecialchars($_POST['app_url'] ?? 'your-app-url'); ?>/admin</code></li>
                            <li>Gunakan email dan password admin yang telah dibuat</li>
                            <li>Setup integrasi (Mikrotik, GenieACS, WhatsApp, dll.)</li>
                            <li>Setup cron job untuk scheduler dan sync</li>
                            <li>Lihat <code>CPANEL_SETUP.md</code> untuk panduan lengkap</li>
                        </ul>
                    </div>

                    <a href="<?php echo htmlspecialchars($_POST['app_url'] ?? '/'); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                        Go to Application
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>