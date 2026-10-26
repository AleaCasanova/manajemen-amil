<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:backup-database';

    /**
     * The console command description.
     */
    protected $description = 'Cadangkan database MySQL ke storage/app/backups secara otomatis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses pencadangan (backup) database amilgsc...');

        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'backup_amilgsc_'.date('Y-m-d_His').'.sql';
        $filepath = $backupDir.DIRECTORY_SEPARATOR.$filename;

        try {
            $tables = DB::select('SHOW TABLES');
            $databaseName = config('database.connections.mysql.database');
            $key = "Tables_in_{$databaseName}";

            $sqlContent = "-- ============================================================\n";
            $sqlContent .= "-- SISTEM MANAJEMEN AMIL GSC - DATABASE BACKUP\n";
            $sqlContent .= '-- Tanggal: '.date('Y-m-d H:i:s')."\n";
            $sqlContent .= "-- Database: {$databaseName}\n";
            $sqlContent .= "-- ============================================================\n\n";
            $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {
                $tableName = $tableObj->$key ?? array_values((array) $tableObj)[0];

                // 1. Structure
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlContent .= $createTable->{'Create Table'}.";\n\n";

                // 2. Data
                $rows = DB::table($tableName)->get();
                if ($rows->isNotEmpty()) {
                    $sqlContent .= "INSERT INTO `{$tableName}` VALUES \n";
                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = array_map(function ($val) {
                            if (is_null($val)) {
                                return 'NULL';
                            }

                            return "'".addslashes((string) $val)."'";
                        }, (array) $row);
                        $values[] = '('.implode(', ', $rowValues).')';
                    }
                    $sqlContent .= implode(",\n", $values).";\n\n";
                }
            }

            $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put($filepath, $sqlContent);

            $fileSize = round(File::size($filepath) / 1024, 2);
            $this->info("✓ Backup database berhasil dibuat: {$filename} ({$fileSize} KB)");

            // Rotasi backup: simpan 10 backup terbaru, hapus backup lama
            $existingBackups = File::glob($backupDir.DIRECTORY_SEPARATOR.'backup_amilgsc_*.sql');
            if (count($existingBackups) > 10) {
                rsort($existingBackups);
                $toDelete = array_slice($existingBackups, 10);
                foreach ($toDelete as $delFile) {
                    File::delete($delFile);
                    $this->comment('Rotasi: Backup lama dihapus -> '.basename($delFile));
                }
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal mencadangkan database: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
