<?php
// Models/LetterCategory.php
declare(strict_types=1);

namespace App\Models;

class LetterCategory extends Model
{
    protected string $table = 'letter_categories';

    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC";
        return $this->db->query($sql)->fetchAll();
    }
}

