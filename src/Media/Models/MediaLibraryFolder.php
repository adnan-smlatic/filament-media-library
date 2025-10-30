<?php

namespace RalphJSmit\Filament\MediaLibrary\Media\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;

class MediaLibraryFolder extends Model
{
    protected $table = 'filament_media_library_folders';

    protected $guarded = [];

    public static function booted(): void
    {
        static::deleting(function (self $mediaLibraryFolder): void {
            $mediaLibraryFolder->children()->lazy()->each(function (self $mediaLibraryFolder) {
                $mediaLibraryFolder->delete();
            });

            $mediaLibraryFolder->mediaLibraryItems()->update([
                'folder_id' => $mediaLibraryFolder->parent_id,
            ]);
        });
    }

    public function deleteRecursive(): void
    {
        $this->children()->lazy()->each(function (self $mediaLibraryFolder) {
            $mediaLibraryFolder->deleteRecursive();
        });

        $this->mediaLibraryItems()->lazy()->each(function (MediaLibraryItem $mediaLibraryItem) {
            $mediaLibraryItem->delete();
        });

        $this->delete();
    }

    public function mediaLibraryItems(): HasMany
    {
        return $this->hasMany(FilamentMediaLibrary::get()->getModelItem(), 'folder_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function getAncestors(?int $level = null): Collection
    {
        return match ($this->getConnection()->getDriverName()) {
            // This default query might work for MySQL as well, but I haven't tested it.
            // I will implement it once the other MySQL query becomes really outdated.
            'mysql' => $this->getAncestorsMysql($level),
            'sqlsrv' => $this->getAncestorsSqlServer($level),
            default => $this->getAncestorsMysqlPgsqlSqlite($level),
        };
    }

    protected function getAncestorsMysql(?int $level = null): Collection
    {
        // Table prefixes are not automatically added by the Eloquent `getTable()` method, so need to manually include them...
        $mediaLibraryFoldersTable = $this->getConnection()->getTablePrefix() . (new static())->getTable();

        return static::query()
            ->select('T2.*')
            ->fromSub(function (Builder $query) use ($mediaLibraryFoldersTable) {
                return $query
                    ->selectRaw(
                        <<<SQL
                        @r AS _id,
                        (SELECT @r := parent_id FROM {$mediaLibraryFoldersTable} WHERE id = _id) AS parent_id,
                        @l := @l + 1 AS level
                    SQL
                    )
                    ->fromRaw(
                        "(SELECT @r := ?, @l := 0) vars,
                        {$mediaLibraryFoldersTable} alias_one",
                        [$this->getKey()]
                    )
                    ->where(new Expression('@r'), '!=', 0);
            }, 'T1')
            ->join(new Expression($mediaLibraryFoldersTable . ' as T2'), 'T1._id', '=', 'T2.id')
            ->when($level, fn (EloquentBuilder $query) => $query->where('T1.level', '<=', $level))
            ->orderByDesc('T1.level')
            ->get();
    }

    /**
     * Works on SQL Server.
     */
    protected function getAncestorsSqlServer(?int $level = null): Collection
    {
        // Table prefixes are not automatically added by the Eloquent `getTable()` method, so need to manually include them...
        $mediaLibraryFoldersTable = $this->getConnection()->getTablePrefix() . (new static())->getTable();

        $folders = DB::select(
            <<<SQL
            WITH FolderHierarchy(_id, parent_id, level) AS (
            SELECT
            alias_one.id AS _id,
            alias_one.parent_id,
            0 AS level
            FROM {$mediaLibraryFoldersTable} alias_one
            WHERE alias_one.id = ?
            UNION ALL
            SELECT
            fm.id AS _id,
            fm.parent_id,
            FolderHierarchy.level + 1
            FROM {$mediaLibraryFoldersTable} fm
            INNER JOIN FolderHierarchy ON fm.id = FolderHierarchy.parent_id
            WHERE FolderHierarchy.level <= ?
            )
            
            SELECT T2.*
            FROM FolderHierarchy
            INNER JOIN {$mediaLibraryFoldersTable} T2 ON FolderHierarchy._id = T2.id
            ORDER BY FolderHierarchy.level DESC
            SQL,
            [$this->getKey(), $level]
        );

        return static::hydrate($folders);
    }

    /**
     * Method works already for MySQL, Postgres and SQLite.
     */
    protected function getAncestorsMysqlPgsqlSqlite(?int $level = null): Collection
    {
        // Table prefixes are not automatically added by the Eloquent `getTable()` method, so need to manually include them...
        $mediaLibraryFoldersTable = $this->getConnection()->getTablePrefix() . (new static())->getTable();

        $folders = DB::select(
            <<<SQL
            WITH RECURSIVE CTE(_id, parent_id, level) AS (
            SELECT
            alias_one.id AS _id,
            alias_one.parent_id,
            0 AS level
            FROM {$mediaLibraryFoldersTable} alias_one
            WHERE alias_one.id = ?
            UNION ALL
            SELECT
            fm.id AS _id,
            fm.parent_id,
            CTE.level + 1
            FROM {$mediaLibraryFoldersTable} fm
            INNER JOIN CTE ON fm.id = CTE.parent_id
            WHERE CTE.level <= ?
            )
            
            SELECT T2.*
            FROM CTE
            INNER JOIN {$mediaLibraryFoldersTable} T2 ON CTE._id = T2.id
            ORDER BY CTE.level DESC
            SQL,
            [$this->getKey(), $level]
        );

        return static::hydrate($folders);
    }
}
