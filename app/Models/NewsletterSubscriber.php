<?php
namespace App\Models;

class NewsletterSubscriber extends BaseModel
{
    protected $table = 'newsletter_subscribers';
    protected $primaryKey = 'id';

    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Subscribe an email. Returns: ['status' => 'subscribed'|'exists', 'id' => int|null]
     */
    public function subscribe(string $email, ?string $ip = null)
    {
        $existing = $this->findByEmail($email);
        if ($existing) {
            return ['status' => 'exists', 'id' => (int)($existing['id'] ?? 0)];
        }

        $id = $this->create([
            'email' => $email,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return ['status' => 'subscribed', 'id' => (int)$id];
    }
}
