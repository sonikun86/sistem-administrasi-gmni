<?php
// Models/User.php
declare(strict_types=1);

namespace App\Models;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function findWithKomisariat(int $id): ?array
    {
        $sql = "SELECT u.*, k.name as komisariat_name, k.code as komisariat_code, k.faculty 
                FROM users u 
                LEFT JOIN komisariats k ON u.komisariat_id = k.id 
                WHERE u.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}

