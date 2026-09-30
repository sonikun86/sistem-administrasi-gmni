<?php
// Database/Migrations.php
declare(strict_types=1);

namespace App\Database;

use App\Config\Database;
use PDO;

class Migrations
{
    public static function run(): void
    {
        $db = Database::getConnection();
        $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);

        $autoInc = $driver === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';
        $textType = $driver === 'sqlite' ? 'TEXT' : 'VARCHAR(255)';
        $longTextType = $driver === 'sqlite' ? 'TEXT' : 'LONGTEXT';

        // 1. Komisariats table
        $db->exec("
            CREATE TABLE IF NOT EXISTS komisariats (
                id {$autoInc},
                code VARCHAR(50) NOT NULL UNIQUE,
                name VARCHAR(150) NOT NULL,
                faculty VARCHAR(150) NULL,
                description {$textType} NULL,
                contact_person VARCHAR(100) NULL,
                phone VARCHAR(30) NULL,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            );
        ");

        // 2. Users table
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id {$autoInc},
                komisariat_id INTEGER NULL,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(120) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(30) NOT NULL, -- 'ketua_besar', 'sekretaris_besar', 'admin_besar', 'dpk'
                position VARCHAR(100) NULL, -- 'Ketua Komisariat', 'Sekretaris', etc.
                phone VARCHAR(30) NULL,
                avatar VARCHAR(255) NULL,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (komisariat_id) REFERENCES komisariats(id) ON DELETE SET NULL
            );
        ");

        // 3. Letter Categories table
        $db->exec("
            CREATE TABLE IF NOT EXISTS letter_categories (
                id {$autoInc},
                code VARCHAR(20) NOT NULL UNIQUE, -- 'SK', 'REK', 'PENG', 'UND', 'EDR', 'LPJ', 'MDT'
                name VARCHAR(100) NOT NULL,
                description {$textType} NULL,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            );
        ");

        // 4. Letters table
        $db->exec("
            CREATE TABLE IF NOT EXISTS letters (
                id {$autoInc},
                tracking_code VARCHAR(50) NOT NULL UNIQUE,
                reference_number VARCHAR(120) NULL, -- Official nomor surat
                category_id INTEGER NOT NULL,
                komisariat_id INTEGER NULL, -- DPK pengirim (jika dari DPK)
                direction VARCHAR(20) NOT NULL, -- 'masuk', 'keluar'
                sender VARCHAR(150) NOT NULL,
                recipient VARCHAR(150) NOT NULL,
                title VARCHAR(255) NOT NULL,
                description {$longTextType} NULL,
                letter_date DATE NOT NULL,
                attachment_path VARCHAR(255) NULL,
                signed_document_path VARCHAR(255) NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'diajukan', 
                -- 'draft', 'diajukan', 'diverifikasi', 'perlu_revisi', 'disetujui', 'ditolak', 'selesai'
                verification_token VARCHAR(64) NOT NULL UNIQUE,
                revision_notes {$longTextType} NULL,
                created_by INTEGER NOT NULL,
                approved_by INTEGER NULL,
                approved_at DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (category_id) REFERENCES letter_categories(id),
                FOREIGN KEY (komisariat_id) REFERENCES komisariats(id) ON DELETE SET NULL,
                FOREIGN KEY (created_by) REFERENCES users(id),
                FOREIGN KEY (approved_by) REFERENCES users(id)
            );
        ");

        // 5. Letter Timelines table (Audit trail)
        $db->exec("
            CREATE TABLE IF NOT EXISTS letter_timelines (
                id {$autoInc},
                letter_id INTEGER NOT NULL,
                user_id INTEGER NULL,
                action VARCHAR(100) NOT NULL,
                status VARCHAR(50) NOT NULL,
                notes {$longTextType} NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (letter_id) REFERENCES letters(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            );
        ");

        // 6. Dispositions table
        $db->exec("
            CREATE TABLE IF NOT EXISTS dispositions (
                id {$autoInc},
                letter_id INTEGER NOT NULL,
                from_user_id INTEGER NOT NULL,
                to_user_id INTEGER NULL,
                to_komisariat_id INTEGER NULL,
                instruction {$textType} NOT NULL, -- 'Tindak Lanjuti', 'Hadir Mewakili', 'Koordinasikan', etc.
                notes {$longTextType} NULL,
                due_date DATE NULL,
                status VARCHAR(30) DEFAULT 'pending', -- 'pending', 'selesai'
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (letter_id) REFERENCES letters(id) ON DELETE CASCADE,
                FOREIGN KEY (from_user_id) REFERENCES users(id),
                FOREIGN KEY (to_user_id) REFERENCES users(id),
                FOREIGN KEY (to_komisariat_id) REFERENCES komisariats(id)
            );
        ");

        // 7. System Settings table
        $db->exec("
            CREATE TABLE IF NOT EXISTS system_settings (
                setting_key VARCHAR(100) PRIMARY KEY,
                setting_value {$longTextType} NULL,
                updated_at DATETIME NULL
            );
        ");
    }
}
