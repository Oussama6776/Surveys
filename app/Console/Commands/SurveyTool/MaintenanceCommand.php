<?php

namespace App\Console\Commands\SurveyTool;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\SurveyFile;
use App\Models\AuditLog;
use Carbon\Carbon;

class MaintenanceCommand extends Command
{
    protected $signature = 'surveytool:maintenance 
                            {--cleanup-files : Clean up orphaned files}
                            {--cleanup-logs : Clean up old audit logs}
                            {--cleanup-cache : Clean up expired cache}
                            {--all : Run all maintenance tasks}
                            {--days=30 : Number of days to keep data}';

    protected $description = 'Perform maintenance tasks for Survey Tool';

    public function handle()
    {
        $this->info('🔧 Running Survey Tool Maintenance...');
        $this->newLine();

        $days = $this->option('days');
        $runAll = $this->option('all');

        if ($runAll || $this->option('cleanup-files')) {
            $this->cleanupFiles($days);
        }

        if ($runAll || $this->option('cleanup-logs')) {
            $this->cleanupLogs($days);
        }

        if ($runAll || $this->option('cleanup-cache')) {
            $this->cleanupCache();
        }

        if (!$runAll && !$this->option('cleanup-files') && !$this->option('cleanup-logs') && !$this->option('cleanup-cache')) {
            $this->showMaintenanceOptions();
        }

        $this->info('✅ Maintenance completed successfully!');
    }

    private function cleanupFiles($days)
    {
        $this->info('📁 Cleaning up orphaned files...');
        
        $cutoffDate = Carbon::now()->subDays($days);
        $orphanedFiles = SurveyFile::where('created_at', '<', $cutoffDate)
            ->whereNull('response_id')
            ->whereNull('question_id')
            ->get();

        $deletedCount = 0;
        $totalSize = 0;

        foreach ($orphanedFiles as $file) {
            try {
                if (Storage::exists($file->file_path)) {
                    $fileSize = Storage::size($file->file_path);
                    Storage::delete($file->file_path);
                    $totalSize += $fileSize;
                }
                $file->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to delete file {$file->id}: " . $e->getMessage());
            }
        }

        $this->line("   Deleted {$deletedCount} orphaned files");
        $this->line("   Freed " . $this->formatBytes($totalSize) . " of storage space");
    }

    private function cleanupLogs($days)
    {
        $this->info('📋 Cleaning up old audit logs...');
        
        $cutoffDate = Carbon::now()->subDays($days);
        $deletedCount = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        
        $this->line("   Deleted {$deletedCount} old audit log entries");
    }

    private function cleanupCache()
    {
        $this->info('🗑️ Cleaning up expired cache...');
        
        // Clear Laravel caches
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');
        
        // Clear custom caches
        $this->clearCustomCaches();
        
        $this->line('   All caches cleared');
    }

    private function clearCustomCaches()
    {
        // Clear analytics cache
        if (config('survey.cache.enabled')) {
            $cachePrefix = config('survey.cache.prefix', 'survey_tool:');
            $keys = \Cache::getRedis()->keys($cachePrefix . '*');
            if (!empty($keys)) {
                \Cache::getRedis()->del($keys);
            }
        }
    }

    private function showMaintenanceOptions()
    {
        $this->info('Available maintenance options:');
        $this->line('  --cleanup-files    Clean up orphaned files');
        $this->line('  --cleanup-logs     Clean up old audit logs');
        $this->line('  --cleanup-cache    Clean up expired cache');
        $this->line('  --all              Run all maintenance tasks');
        $this->line('  --days=30          Number of days to keep data (default: 30)');
        $this->newLine();
        $this->line('Examples:');
        $this->line('  php artisan surveytool:maintenance --all');
        $this->line('  php artisan surveytool:maintenance --cleanup-files --days=7');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}