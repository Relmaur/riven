<?php

declare(strict_types=1);

namespace App\Models;

use Core\Database;
use PhpParser\Node\Expr\Cast;

class Post
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all posts from the database
     */
    public function getAllPosts(): array
    {
        /*
           =============
              BEFORE ORM
           =============
        */
        // $this->db->query("
        //     SELECT posts.*, users.name AS author_name
        //     FROM posts
        //     LEFT JOIN users ON posts.author_id = users.id
        //     ORDER BY posts.created_at DESC
        // ");
        // return $this->db->fetchAll();

        /*
           ==========
              AFTER:
           ==========
        */
        // Note: We'll add JOIN support to Query Builder in the next step
        // For now, we'll use a raw query for the JOIN, but everything else uses the builder
        return $this->db->table('posts')
            ->leftJoin('users', 'posts.author_id', '=', 'users.id')
            ->select(['posts.*', 'users.name as author_name'])
            ->orderBy('posts.created_at', 'desc')
            ->get();
    }

    /**
     * Get a single post by its ID.
     */
    public function getPostById($id): ?object
    {
        /*
           =============
              BEFORE ORM
           =============
        */
        // $this->db->query("
        //     SELECT posts.*, users.name AS author_name
        //     FROM posts
        //     LEFT JOIN users ON posts.author_id = users.id
        //     WHERE posts.id = :id
        // ");
        // $this->db->bind(':id', $id);
        // return $this->db->fetch();

        /*
           =============
              AFTER ORM
           =============
        */
        // Still need JOIN for author name, but the rest is clean
        return $this->db->table('posts')
            ->leftJoin('users', 'posts.author_id', '=', 'users.id')
            ->select(['posts.*', 'users.name as author_name'])
            ->where('posts.id', $id)
            ->first();
    }

    /**
     * Create a new post
     */
    public function createPost($data): int|string
    {


        /*
           =============
              BEFORE ORM
           =============
        */
        // $this->db->query("INSERT INTO posts (title, content, author_id, image_path) VALUES (:title, :content, :author_id, :image_path)");

        // // Bind values
        // $this->db->bind(':title', $data['title']);
        // $this->db->bind(':content', $data['content']);
        // $this->db->bind(':author_id', $data['author_id']);
        // $this->db->bind(':image_path', $data['image_path']);

        // // Execute
        // if ($this->db->execute()) {
        //     return true;
        // } else {
        //     return false;
        // }

        /*
           ==========
              AFTER
           ==========
        */
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->table('posts')->insert($data);
    }

    /**
     * Update a post.
     */
    public function updatePost($data): int
    {
        /*
           =============
              BEFORE ORM
           =============
        */
        // $this->db->query("UPDATE posts SET title = :title, content = :content, image_path = :image_path WHERE id = :id");

        // // Bind values
        // $this->db->bind(':id', $data['id']);
        // $this->db->bind(':title', $data['title']);
        // $this->db->bind(':content', $data['content']);
        // $this->db->bind(':image_path',  $data['image_path']);

        // // Execute
        // if ($this->db->execute()) {
        //     return true;
        // } else {
        //     return false;
        // }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->table('posts')
            ->where('id', $data['id'])
            ->update($data);
    }

    /**
     * Delete a post.
     */
    public function deletePost($id): int
    {
        /*
           =============
              BEFORE ORM
           =============
        */
        // $this->db->query("DELETE FROM posts WHERE id = :id");
        // $this->db->bind(':id', $id);

        // // Execute
        // if ($this->db->execute()) {
        //     return true;
        // } else {
        //     return false;
        // }

        /*
           ==========
              AFTER
           ==========
        */
        return $this->db->table('posts')
            ->where('id', $id)
            ->delete();
    }

    /**
     * Get published posts only
     * 
     * @return array
     */
    public function getPublished(): array
    {
        return $this->db->table('posts')
            ->leftJoin('users', 'posts.author_id', '=', 'users.id')
            ->select(['posts.*', 'users.name as author_name'])
            ->where('posts.status', 'published')
            ->orderBy('posts.created_at', 'desc')
            ->get();
    }

    /**
     * Get posts by author
     * 
     * @param int $authorId
     * @return array
     */
    public function getByAuthor($authorId): array
    {
        return $this->db->table('posts')
            ->leftJoin('users', 'posts.author_id', '=', 'users.id')
            ->select(['posts.*', 'users.name as author_name'])
            ->where('posts.author_id', $authorId)
            ->orderBy('posts.created_at', 'desc')
            ->get();
    }

    /**
     * Search posts by title or content
     * 
     * @param string $query
     * @return array
     */
    public function search($query): array
    {
        return $this->db->table('posts')
            ->where('title', 'LIKE', '%' . $query . '%')
            ->get();
    }

    /**
     * Get latest posts with limit
     * 
     * @param int $limit
     * @return array
     */
    public function getLatest($limit = 10): array
    {
        return $this->db->table('posts')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Count all posts
     * 
     * @return int
     */
    public function count(): int
    {
        return $this->db->table('posts')->count();
    }

    /**
     * Check if a post exists by ID
     * 
     * @param int $id
     * @return bool
     */
    public function exists($id): bool
    {
        return $this->db->table('posts')
            ->where('id', $id)
            ->exists();
    }
}
