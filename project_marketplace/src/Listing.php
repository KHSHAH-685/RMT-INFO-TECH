<?php
declare(strict_types=1);

class Listing {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getActiveListings(): array {
        $sql = "SELECT id, title, slug, tagline, category, asking_price, 
                       monthly_revenue, monthly_profit, primary_tech_stack, status 
                FROM listings 
                WHERE status = 'available' 
                ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array {
        $sql = "SELECT l.*, u.name AS seller_name 
                FROM listings l
                JOIN users u ON l.seller_id = u.id 
                WHERE l.slug = :slug LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function lockForEscrow(int $listingId): bool {
        $sql = "UPDATE listings 
                SET status = 'escrow_pending' 
                WHERE id = :id AND status = 'available'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $listingId]);
        return $stmt->rowCount() > 0;
    }
}