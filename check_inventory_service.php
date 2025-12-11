<?php
// check_manager_transaction_service.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING MANAGERTRANSACTIONSERVICE ===\n\n";

// 1. Cek apakah file ada
$servicePath = 'app/Services/ManagerTransactionService.php';
if (file_exists($servicePath)) {
    echo "✅ File ditemukan: $servicePath\n\n";
    
    // 2. Baca isi file
    $content = file_get_contents($servicePath);
    echo "📄 Methods dalam ManagerTransactionService:\n";
    
    // Ekstrak semua public methods
    preg_match_all('/public function (\w+)/', $content, $matches);
    foreach ($matches[1] as $method) {
        echo "  - $method\n";
    }
    
    echo "\n";
    
    // 3. Tampilkan namespace/class name
    preg_match('/namespace (.*?);/', $content, $namespaceMatch);
    preg_match('/class (\w+)/', $content, $classMatch);
    
    if ($namespaceMatch && $classMatch) {
        $fullClassName = $namespaceMatch[1] . '\\' . $classMatch[1];
        echo "🔧 Full Class Name: $fullClassName\n\n";
    }
    
} else {
    echo "❌ File ManagerTransactionService.php TIDAK ditemukan\n";
    echo "\n🔍 Mencari file serupa...\n";
    
    // Cari file dengan nama serupa
    $similarFiles = shell_exec('find app/Services -name "*Manager*" -o -name "*Transaction*" | grep -i "service"');
    if ($similarFiles) {
        echo "File serupa yang ditemukan:\n";
        echo $similarFiles;
    }
    exit(1);
}

// 4. Cek apakah digunakan di controllers
echo "🔍 Cek penggunaan di Controllers:\n";
$controllers = glob('app/Http/Controllers/*.php');
$found = false;

foreach ($controllers as $controller) {
    $content = file_get_contents($controller);
    $controllerName = basename($controller);
    
    // Cek dengan beberapa pattern
    if (strpos($content, 'ManagerTransactionService') !== false || 
        strpos($content, 'ManagerTransaction') !== false) {
        echo "  ✅ Digunakan di: $controllerName\n";
        
        // Tampilkan baris yang menggunakan
        $lines = file($controller);
        foreach ($lines as $lineNumber => $line) {
            if (strpos($line, 'ManagerTransaction') !== false) {
                echo "     Line " . ($lineNumber + 1) . ": " . trim($line) . "\n";
            }
        }
        $found = true;
    }
}

// Cek juga di folder Controller subdirectories
$subdirControllers = glob('app/Http/Controllers/**/*.php');
foreach ($subdirControllers as $controller) {
    $content = file_get_contents($controller);
    $controllerName = str_replace('app/Http/Controllers/', '', $controller);
    
    if (strpos($content, 'ManagerTransactionService') !== false || 
        strpos($content, 'ManagerTransaction') !== false) {
        echo "  ✅ Digunakan di: $controllerName\n";
        
        // Tampilkan baris yang menggunakan
        $lines = file($controller);
        foreach ($lines as $lineNumber => $line) {
            if (strpos($line, 'ManagerTransaction') !== false) {
                echo "     Line " . ($lineNumber + 1) . ": " . trim($line) . "\n";
            }
        }
        $found = true;
    }
}

if (!$found) {
    echo "  ❌ TIDAK digunakan di Controller manapun\n";
}

// 5. Cek apakah ada di Service Provider atau Dependency Injection
echo "\n🔍 Cek di Service Providers:\n";
$providers = glob('app/Providers/*.php');
$providerFound = false;

foreach ($providers as $provider) {
    $content = file_get_contents($provider);
    if (strpos($content, 'ManagerTransaction') !== false) {
        echo "  ✅ Referensi ditemukan di: " . basename($provider) . "\n";
        $providerFound = true;
    }
}

// 6. Cek apakah ada binding di AppServiceProvider
echo "\n🔍 Cek binding di AppServiceProvider:\n";
$appServiceProvider = 'app/Providers/AppServiceProvider.php';
if (file_exists($appServiceProvider)) {
    $content = file_get_contents($appServiceProvider);
    if (preg_match('/ManagerTransactionService/', $content)) {
        echo "  ✅ Ada binding di AppServiceProvider\n";
        
        // Tampilkan bagian binding
        $lines = file($appServiceProvider);
        $inRegisterSection = false;
        foreach ($lines as $lineNumber => $line) {
            if (strpos($line, 'public function register()') !== false) {
                $inRegisterSection = true;
            }
            if ($inRegisterSection && strpos($line, 'ManagerTransaction') !== false) {
                echo "     Line " . ($lineNumber + 1) . ": " . trim($line) . "\n";
            }
            if ($inRegisterSection && strpos($line, '}') !== false) {
                $inRegisterSection = false;
            }
        }
    } else {
        echo "  ❌ Tidak ada binding di AppServiceProvider\n";
    }
}

// 7. Summary
echo "\n=== SUMMARY ===\n";
if ($found || $providerFound) {
    echo "✅ ManagerTransactionService DIGUNAKAN di aplikasi\n";
    echo "\n📋 Detail penggunaan:\n";
    echo "- " . ($found ? "Digunakan di Controller" : "Tidak digunakan di Controller") . "\n";
    echo "- " . ($providerFound ? "Ada di Service Provider" : "Tidak ada di Service Provider") . "\n";
} else {
    echo "⚠️  ManagerTransactionService TIDAK DIGUNAKAN (kemungkinan dead code)\n";
    echo "\n🔧 Rekomendasi:\n";
    echo "1. Hapus file jika benar-benar tidak digunakan\n";
    echo "   rm app/Services/ManagerTransactionService.php\n";
    echo "2. Atau integrasikan ke controller yang sesuai\n";
    echo "3. Backup dulu jika ragu\n";
    echo "   cp app/Services/ManagerTransactionService.php app/Services/ManagerTransactionService.php.backup\n";
}

// 8. Bonus: Cek apakah ada class yang extend/menggunakan
echo "\n🔍 Cek class lain yang mungkin menggunakan:\n";
$allPhpFiles = shell_exec('find app -name "*.php" -type f | grep -v vendor | grep -v node_modules');
$filesArray = explode("\n", trim($allPhpFiles));
$usageCount = 0;

foreach ($filesArray as $file) {
    if (empty($file) || !file_exists($file)) continue;
    
    $content = file_get_contents($file);
    if (preg_match('/use.*ManagerTransactionService|new ManagerTransactionService|ManagerTransactionService::/', $content)) {
        echo "  📍 Digunakan di: " . str_replace('app/', '', $file) . "\n";
        $usageCount++;
    }
}

if ($usageCount === 0) {
    echo "  ❌ Tidak ditemukan penggunaan di file PHP manapun\n";
}