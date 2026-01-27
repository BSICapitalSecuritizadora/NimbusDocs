<?php

declare(strict_types=1);

namespace App\Application\Service;

use PDO;

/**
 * Global Search Service
 * Searches across multiple entities: submissions, users, documents
 */
class GlobalSearchService
{
    public function __construct(
        private PDO $pdo
    ) {}

    /**
     * Perform a global search across multiple entities
     * 
     * @param string $query Search query
     * @param int $limit Maximum results per category
     * @return array<string, array> Grouped search results
     */
    public function search(string $query, int $limit = 10): array
    {
        $query = trim($query);
        
        if (strlen($query) < 2) {
            return [
                'submissions' => [],
                'users' => [],
                'documents' => [],
                'total' => 0,
            ];
        }

        $searchTerm = '%' . $query . '%';

        $results = [
            'submissions' => $this->searchSubmissions($searchTerm, $limit),
            'users' => $this->searchUsers($searchTerm, $limit),
            'documents' => $this->searchDocuments($searchTerm, $limit),
        ];

        $results['total'] = count($results['submissions']) 
            + count($results['users']) 
            + count($results['documents']);

        return $results;
    }

    /**
     * Search in submissions
     */
    private function searchSubmissions(string $searchTerm, int $limit): array
    {
        $sql = "
            SELECT 
                s.id,
                s.reference_code,
                s.title,
                s.status,
                s.submitted_at,
                u.full_name AS user_name,
                u.email AS user_email,
                'submission' AS type
            FROM portal_submissions s
            JOIN portal_users u ON u.id = s.portal_user_id
            WHERE s.reference_code LIKE :term1
               OR s.title LIKE :term2
               OR s.company_name LIKE :term3
               OR u.full_name LIKE :term4
               OR u.email LIKE :term5
            ORDER BY s.submitted_at DESC
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':term1', $searchTerm);
        $stmt->bindValue(':term2', $searchTerm);
        $stmt->bindValue(':term3', $searchTerm);
        $stmt->bindValue(':term4', $searchTerm);
        $stmt->bindValue(':term5', $searchTerm);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Search in users (portal users)
     */
    private function searchUsers(string $searchTerm, int $limit): array
    {
        $sql = "
            SELECT 
                id,
                full_name,
                email,
                document_number,
                status,
                created_at,
                'user' AS type
            FROM portal_users
            WHERE full_name LIKE :term1
               OR email LIKE :term2
               OR document_number LIKE :term3
               OR external_id LIKE :term4
            ORDER BY created_at DESC
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':term1', $searchTerm);
        $stmt->bindValue(':term2', $searchTerm);
        $stmt->bindValue(':term3', $searchTerm);
        $stmt->bindValue(':term4', $searchTerm);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Search in general documents
     */
    private function searchDocuments(string $searchTerm, int $limit): array
    {
        $sql = "
            SELECT 
                id,
                title,
                file_original_name AS file_name,
                description,
                created_at,
                'document' AS type
            FROM general_documents
            WHERE title LIKE :term1
               OR description LIKE :term2
               OR file_original_name LIKE :term3
            ORDER BY created_at DESC
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':term1', $searchTerm);
        $stmt->bindValue(':term2', $searchTerm);
        $stmt->bindValue(':term3', $searchTerm);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Quick search - returns minimal data for autocomplete
     */
    public function quickSearch(string $query, int $limit = 5): array
    {
        $query = trim($query);
        
        if (strlen($query) < 2) {
            return [];
        }

        $searchTerm = '%' . $query . '%';
        $results = [];

        // Quick search submissions
        $sql = "
            SELECT 'submission' AS type, id, reference_code AS label, title AS description
            FROM portal_submissions
            WHERE reference_code LIKE :term OR title LIKE :term2
            LIMIT :lim
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':term', $searchTerm);
        $stmt->bindValue(':term2', $searchTerm);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);

        // Quick search users
        $sql = "
            SELECT 'user' AS type, id, full_name AS label, email AS description
            FROM portal_users
            WHERE full_name LIKE :term OR email LIKE :term2
            LIMIT :lim
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':term', $searchTerm);
        $stmt->bindValue(':term2', $searchTerm);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);

        return array_slice($results, 0, $limit * 2);
    }
}
