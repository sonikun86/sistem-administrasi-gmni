<?php
// Models/LetterTimeline.php
declare(strict_types=1);

namespace App\Models;

class LetterTimeline extends Model
{
    protected string $table = 'letter_timelines';

    public function getByLetter(int $letterId): array
    {
        $sql = "SELECT t.*, u.name as user_name, u.role as user_role 
                FROM {$this->table} t 
                LEFT JOIN users u ON t.user_id = u.id 
                WHERE t.letter_id = :letter_id 
                ORDER BY t.created_at ASC, t.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['letter_id' => $letterId]);
        return $stmt->fetchAll();
    }

    public static function log(int $letterId, ?int $userId, string $action, string $status, ?string $notes = null): int
    {
        $instance = new self();
        return $instance->create([
            'letter_id' => $letterId,
            'user_id' => $userId,
            'action' => $action,
            'status' => $status,
            'notes' => $notes,
        ]);
    }
}

