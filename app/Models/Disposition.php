<?php
// Models/Disposition.php
declare(strict_types=1);

namespace App\Models;

class Disposition extends Model
{
    protected string $table = 'dispositions';

    public function getByLetter(int $letterId): array
    {
        $sql = "SELECT d.*, 
                       creator.name as from_user_name,
                       target_u.name as to_user_name,
                       target_k.name as to_komisariat_name
                FROM {$this->table} d
                LEFT JOIN users creator ON d.from_user_id = creator.id
                LEFT JOIN users target_u ON d.to_user_id = target_u.id
                LEFT JOIN komisariats target_k ON d.to_komisariat_id = target_k.id
                WHERE d.letter_id = :letter_id
                ORDER BY d.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['letter_id' => $letterId]);
        return $stmt->fetchAll();
    }
}

