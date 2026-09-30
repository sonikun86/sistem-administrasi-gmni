<?php
// Models/Letter.php
declare(strict_types=1);

namespace App\Models;

use PDO;

class Letter extends Model
{
    protected string $table = 'letters';

    public function getFiltered(array $filters = [], int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT l.*, 
                       c.name as category_name, c.code as category_code,
                       k.name as dpk_name, k.code as dpk_code,
                       u.name as creator_name,
                       approver.name as approver_name
                FROM {$this->table} l
                LEFT JOIN letter_categories c ON l.category_id = c.id
                LEFT JOIN komisariats k ON l.komisariat_id = k.id
                LEFT JOIN users u ON l.created_by = u.id
                LEFT JOIN users approver ON l.approved_by = approver.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['direction'])) {
            $sql .= " AND l.direction = :direction";
            $params['direction'] = $filters['direction'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND l.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['komisariat_id'])) {
            $sql .= " AND l.komisariat_id = :komisariat_id";
            $params['komisariat_id'] = $filters['komisariat_id'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND l.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (l.title LIKE :search OR l.reference_number LIKE :search OR l.tracking_code LIKE :search OR l.sender LIKE :search OR l.recipient LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND l.letter_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND l.letter_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY l.id DESC LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findWithDetails(int|string $id): ?array
    {
        $sql = "SELECT l.*, 
                       c.name as category_name, c.code as category_code,
                       k.name as dpk_name, k.code as dpk_code, k.faculty as dpk_faculty,
                       u.name as creator_name, u.email as creator_email,
                       approver.name as approver_name
                FROM {$this->table} l
                LEFT JOIN letter_categories c ON l.category_id = c.id
                LEFT JOIN komisariats k ON l.komisariat_id = k.id
                LEFT JOIN users u ON l.created_by = u.id
                LEFT JOIN users approver ON l.approved_by = approver.id
                WHERE l.id = :id LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function findByVerificationToken(string $token): ?array
    {
        $sql = "SELECT l.*, 
                       c.name as category_name, c.code as category_code,
                       k.name as dpk_name, k.code as dpk_code,
                       approver.name as approver_name
                FROM {$this->table} l
                LEFT JOIN letter_categories c ON l.category_id = c.id
                LEFT JOIN komisariats k ON l.komisariat_id = k.id
                LEFT JOIN users approver ON l.approved_by = approver.id
                WHERE l.verification_token = :token LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Generate format penomoran resmi surat GMNI
     */
    public function generateOfficialNumber(int $categoryId, ?int $komisariatId = null, string $date = ''): string
    {
        $cat = (new LetterCategory())->find($categoryId);
        $catCode = $cat['code'] ?? 'SURAT';

        $timestamp = $date ? strtotime($date) : time();
        $year = date('Y', $timestamp);
        $monthNum = (int)date('n', $timestamp);

        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romans[$monthNum] ?? 'I';

        // Hitung nomor urut tahun ini
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE strftime('%Y', created_at) = :year AND reference_number IS NOT NULL AND reference_number != ''";
        // Check for MySQL support
        if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            $sql = "SELECT COUNT(*) FROM {$this->table} 
                    WHERE YEAR(created_at) = :year AND reference_number IS NOT NULL AND reference_number != ''";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['year' => $year]);
        $count = (int)$stmt->fetchColumn() + 1;
        $order = sprintf('%03d', $count);

        if ($komisariatId) {
            $kom = (new Komisariat())->find($komisariatId);
            $subCode = $kom ? strtoupper(str_replace(' ', '-', $kom['code'])) : 'DPK';
            return "{$order}/{$catCode}/DPK-GMNI-{$subCode}/UNISDA/{$romanMonth}/{$year}";
        }

        // Default Komisariat Besar GMNI UNISDA
        return "{$order}/{$catCode}/KOM-BESAR/GMNI-UNISDA/{$romanMonth}/{$year}";
    }
}

