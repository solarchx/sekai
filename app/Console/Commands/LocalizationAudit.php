<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LocalizationAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:localization
                            {--o|output= : Path to write JSON report (default real_localization_audit.json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan blade views and report any strings not wrapped in __() or trans()';

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $this->info('Starting localization audit...');

        $outputPath = $this->option('output') ?: base_path('real_localization_audit.json');
        $bladeDir = resource_path('views');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($bladeDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $bladeFiles = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.php') !== false) {
                $bladeFiles[] = $file->getPathname();
            }
        }

        sort($bladeFiles);

        $totalFiles = count($bladeFiles);
        $compliantFiles = [];
        $filesWithIssues = [];
        $totalIssues = 0;

        foreach ($bladeFiles as $filePath) {
            $relative = str_replace($bladeDir . DIRECTORY_SEPARATOR, '', $filePath);
            $content = $this->files->get($filePath);
            $lines = explode("\n", $content);

            $issues = [];

            foreach ($lines as $idx => $line) {
                $lineNum = $idx + 1;
                // skip blade comments and directives
                if (strpos($line, '{{--') !== false || preg_match('/^\s*@/', $line)) {
                    continue;
                }

                $patterns = [
                    '/<option[^>]*>([^<]*[A-Z][a-zA-Z\s]*)<\/option>/',
                    '/<(?:button|a)[^>]*>[\s]*([A-Z][a-zA-Z\s]*?)[\s]*<\/(?:button|a)>/i',
                    '/<label[^>]*>[\s]*([A-Z][a-zA-Z\s]*?)[\s]*<\/label>/i',
                    '/<(?:h[1-6])[^>]*>([^<{]*[A-Z][a-zA-Z\s]*?)<\/h[1-6]>/i',
                    '/<span[^>]*title=["\']([^"\']+)["\']/',
                    '/<p[^>]*>[\s]*([A-Z][a-zA-Z\s,]+?)[\s]*<\/p>/i',
                ];

                foreach ($patterns as $pat) {
                    if (preg_match_all($pat, $line, $matches)) {
                        foreach ($matches[1] as $text) {
                            $text = trim($text);
                            if (strlen($text) < 3) continue;
                            if (str_contains($line, '__') || str_contains($line, 'trans(')) continue;
                            if (preg_match('/\$|{{|}}/', $text)) continue;
                            if (preg_match('/^\d+$/', $text)) continue;
                            if (in_array($text, ['N/A', 'ACTIVE', 'DELETED', 'VP', 'Admin', 'Student', 'PERIOD UNAVAILABLE'])) {
                                continue;
                            }

                            $issues[$lineNum][] = [
                                'text' => $text,
                                'preview' => trim($line),
                            ];
                        }
                    }
                }
            }

            if (!empty($issues)) {
                $filesWithIssues[$relative] = $issues;
                $totalIssues += array_reduce($issues, fn($carry, $arr) => $carry + count($arr), 0);
                $lineNumbers = implode(', ', array_keys($issues));
                $this->warn("Needs work: $relative (lines: $lineNumbers, " . count($issues) . " unique lines, $totalIssues total strings)");
            } else {
                $compliantFiles[] = $relative;
            }
        }

        $this->line('');
        $this->info("Total blade files scanned: $totalFiles");
        $this->info("Files with issues: " . count($filesWithIssues));
        $this->info("Total possible issues: $totalIssues");

        if ($totalIssues === 0) {
            $this->info("✅ All files look properly localized.");
        } else {
            $this->warn("⚠️  $totalIssues strings may need __() or trans() wrapping.");
        }

        $report = [
            'timestamp' => now()->toDateTimeString(),
            'total_files' => $totalFiles,
            // include line counts and text for easier review
            'files_with_issues' => $filesWithIssues,
            'total_issues' => $totalIssues,
        ];

        $this->files->put($outputPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info("Detailed report written to: $outputPath");

        return 0;
    }
}
