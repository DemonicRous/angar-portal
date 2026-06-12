<?php

namespace App\Models;

use App\Core\Model;

/**
 * Модель роли.
 * Таблица roles.
 */
class Role extends Model
{
    protected static string $table = 'roles';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['name'];
}