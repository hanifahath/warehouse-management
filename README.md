# 🏭 Warehouse Management System (WMS)

Sebuah sistem manajemen gudang komprehensif berbasis web untuk mengelola inventori, pesanan restok, dan pelacakan stok dengan antarmuka yang user-friendly.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

## ✨ Fitur Utama

### 📦 **Manajemen Inventori Lengkap**
- ✅ **Produk Management** - Tambah, edit, hapus produk dengan SKU unik
- ✅ **Kategori Produk** - Organisasi produk dalam kategori
- ✅ **Stok Real-time** - Pelacakan stok otomatis
- ✅ **Alert System** - Notifikasi stok rendah & habis
- ✅ **Lokasi Rak** - Manajemen penyimpanan fisik
- ✅ **Unit Management** - Multiple unit measurement

### 📋 **Sistem Restock Otomatis**
- ✅ **Purchase Order** - Generate PO otomatis
- ✅ **Status Tracking** - Pending → Confirmed → In Transit → Received
- ✅ **Supplier Management** - Multiple supplier support
- ✅ **Approval Workflow** - Multi-level approval system
- ✅ **History & Audit** - Complete order history

### 👥 **Multi-User dengan RBAC**
- ✅ **Role-Based Access Control** - 4 level user roles
- ✅ **Permission System** - Fine-grained permissions
- ✅ **Activity Log** - Audit trail semua aktivitas
- ✅ **Profile Management** - User profile customization

### 📊 **Reporting & Analytics**
- ✅ **Dashboard Real-time** - Key metrics at a glance
- ✅ **Stock Reports** - Inventory analysis
- ✅ **Restock Reports** - Supplier performance
- ✅ **Export Capabilities** - PDF, Excel, CSV
- ✅ **Chart Visualization** - Data visualization

## 🏗️ **Arsitektur Sistem**

### Tech Stack
- **Backend**: Laravel 10, PHP 8.1+
- **Frontend**: Bootstrap 5, Chart.js, Font Awesome
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Breeze
- **Development**: Composer, NPM, Git

### Database Schema
```sql
users → roles → permissions
products → categories → stock_movements
restock_orders → restock_items → suppliers
```

## 🚀 **Instalasi & Setup**

### Prerequisites
- PHP 8.1 atau lebih tinggi
- Composer 2.0+
- MySQL 8.0+ atau MariaDB 10.4+
- Node.js 16+ & NPM 8+
- Web server (Apache/Nginx)

### Step 1: Clone Repository
```bash
git clone https://github.com/hanifahath/warehouse-management.git
cd warehouse-management
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
npm run build
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouse_db
DB_USERNAME=root
DB_PASSWORD=

# Untuk development
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### Step 5: Database Migration & Seeding
```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Atau seed individual
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductSeeder
php artisan db:seed --class=RestockOrderSeeder
```

### Step 6: Storage Setup
```bash
# Create storage link
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Step 7: Run Application
```bash
# Development server
php artisan serve

# Atau dengan port tertentu
php artisan serve --port=8080
```

Akses aplikasi di: **http://localhost:8000**

## 👥 **Default Login Credentials**

| Role | Email | Password | Hak Akses |
|------|-------|----------|-----------|
| **Admin** | `admin@inventory.test` | `password123` | Full system access |
| **Manager** | `manager@inventory.test` | `password123` | Manage operations |
| **Staff** | `staff1@inventory.test` | `password123` | Daily operations |
| **Supplier 1** | `supplier1@inventory.test` | `password123` | View assigned orders |

## 🔧 **Development Commands**

### Common Artisan Commands
```bash
# Run migrations
php artisan migrate
php artisan migrate:fresh
php artisan migrate:rollback

# Run seeders
php artisan db:seed
php artisan db:seed --class=SpecificSeeder

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Generate resources
php artisan make:model Product -mcr
php artisan make:controller ProductController --resource
php artisan make:seeder ProductSeeder
php artisan make:factory ProductFactory

# List routes
php artisan route:list
```

### NPM Commands
```bash
# Development
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

## 🧪 **Testing**

### Unit Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=ProductTest

# Run with coverage
php artisan test --coverage
```

### Database Testing
```bash
# Refresh database and run tests
php artisan test --parallel

# Run feature tests
php artisan test --testsuite=Feature
```

### Manual Testing Data
```bash
# Generate test products
php artisan tinker
>>> Product::factory()->count(10)->create()

# Check database state
>>> \DB::table('products')->count()
>>> \DB::table('restock_orders')->where('status', 'Pending')->count()
```

## 📊 **Sample Data**

Setelah menjalankan seeder, Anda akan mendapatkan:

- **10+ Kategori** produk (Electronics, Office Equipment, dll)
- **30+ Produk** dengan data lengkap
- **10+ Restock Orders** dengan berbagai status
- **6 User** dengan role berbeda
- **Sample Images** dari Unsplash untuk produk & kategori

## 🔐 **Security Features**

- **Password Hashing** - Bcrypt algorithm
- **CSRF Protection** - Laravel built-in
- **XSS Protection** - Blade templating
- **SQL Injection Prevention** - Eloquent ORM
- **Session Security** - Encrypted cookies
- **Role-Based Middleware** - Custom middleware
- **Input Validation** - Form request validation

## 📈 **Performance Optimization**

### Caching Strategy
```php
// Cache frequently accessed data
Cache::remember('categories', 3600, function () {
    return Category::all();
});

Cache::remember('low-stock-products', 300, function () {
    return Product::where('current_stock', '<=', 'min_stock')->get();
});
```

### Database Indexing
```sql
-- Recommended indexes
CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_restock_orders_status ON restock_orders(status);
CREATE INDEX idx_restock_orders_supplier ON restock_orders(supplier_id);
```

## 🚨 **Troubleshooting**

### Common Issues & Solutions

#### Issue 1: "SQLSTATE[HY000] [2002] Connection refused"
```bash
# Solution: Check MySQL service
sudo service mysql start
# atau
mysql.server start
```

#### Issue 2: "Class 'Faker\Factory' not found"
```bash
# Solution: Install Faker
composer require fakerphp/faker --dev
```

#### Issue 3: "No application encryption key has been specified"
```bash
# Solution: Generate app key
php artisan key:generate
```

#### Issue 4: "Images not loading"
```bash
# Solution: Create storage link
php artisan storage:link
# dan set permissions
chmod -R 755 storage/public
```

#### Issue 5: "Migration error: foreign key constraint"
```bash
# Solution: Disable FK checks temporarily
DB::statement('SET FOREIGN_KEY_CHECKS=0');
// Your migration code
DB::statement('SET FOREIGN_KEY_CHECKS=1');
```

### Debug Mode
```env
# .env file
APP_DEBUG=true
APP_ENV=local

# Log level
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

## 📚 **API Documentation**

### Available Endpoints

#### Products API
```
GET    /api/products          # List all products
GET    /api/products/{id}     # Get specific product
POST   /api/products          # Create new product
PUT    /api/products/{id}     # Update product
DELETE /api/products/{id}     # Delete product
```

#### Restock Orders API
```
GET    /api/restock-orders             # List orders
POST   /api/restock-orders             # Create order
PUT    /api/restock-orders/{id}/status # Update status
GET    /api/restock-orders/reports     # Generate reports
```

### Sample API Request
```bash
# Get all products
curl -X GET http://localhost:8000/api/products \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {token}"
```

## 🔄 **Deployment**

### Production Setup
```bash
# Environment setup
cp .env.example .env.production
# Edit production settings

# Install dependencies (no dev)
composer install --optimize-autoloader --no-dev

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data /var/www/warehouse
chmod -R 755 storage bootstrap/cache
```

### Server Requirements
- **PHP**: 8.1+ with extensions: mbstring, xml, curl, json, openssl
- **Database**: MySQL 8.0+ atau MariaDB 10.4+
- **Web Server**: Apache dengan mod_rewrite atau Nginx
- **Memory**: Minimum 512MB RAM
- **Storage**: 100MB+ free space

## 📝 **Changelog**

### v1.0.0 (Initial Release)
- ✅ Basic inventory management
- ✅ Restock order system
- ✅ Multi-user authentication
- ✅ Dashboard with charts
- ✅ Sample data seeder
- ✅ Responsive design

### v1.1.0 (Planned)
- [ ] Barcode scanning support
- [ ] Mobile-responsive improvements
- [ ] Advanced reporting
- [ ] Export to PDF/Excel
- [ ] Email notifications

## 🤝 **Contributing**

Kontribusi sangat diterima! Ikuti langkah berikut:

1. **Fork** repository
2. **Buat branch** untuk fitur baru (`git checkout -b feature/awesome-feature`)
3. **Commit** perubahan (`git commit -m 'Add awesome feature'`)
4. **Push** ke branch (`git push origin feature/awesome-feature`)
5. **Buat Pull Request**

### Coding Standards
- Ikuti [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Gunakan English untuk comments dan commit messages
- Tambahkan tests untuk fitur baru
- Update documentation sesuai perubahan

## 📄 **License**

Proyek ini dilisensikan di bawah **MIT License** - lihat file [LICENSE](LICENSE) untuk detail.

## 🙏 **Acknowledgments**

- [Laravel](https://laravel.com) - PHP Framework yang luar biasa
- [Bootstrap](https://getbootstrap.com) - CSS framework
- [Unsplash](https://unsplash.com) - Gambar sample produk
- [Font Awesome](https://fontawesome.com) - Ikon
- [Chart.js](https://chartjs.org) - Library chart

## 📞 **Support**

Untuk bantuan atau pertanyaan:

- **Email**: hanifahatthahirahbasir@gmail.com
- **Issue Tracker**: [GitHub Issues](https://github.com/hanifahath/warehouse-management/issues)
- **Documentation**: [Wiki](https://github.com/hanifahath/warehouse-management/wiki)

---

**Dikembangkan dengan ❤️ oleh [Hanifah Atthahira Basir]** - © 2024 Warehouse Management System