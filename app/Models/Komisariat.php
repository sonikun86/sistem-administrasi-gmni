<?php
// Models/Komisariat.php
declare(strict_types=1);

namespace App\Models;

class Komisariat extends Model
{
    protected string $table = 'komisariats';

    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByCode(string $code): ?array
    {
        return $this->findBy('code', $code);
    }
}

