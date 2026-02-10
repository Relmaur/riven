<?php

declare(strict_types=1);

namespace Core\Interfaces;

interface MigrationInterface
{
    public function up(): void;
    public function down(): void;
}
