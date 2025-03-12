<?php

namespace App\Console\Commands;

use App\Services\TranslationService;
use Illuminate\Console\Command;
use App\Models\Translation;

class TranslationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:manage
                          {action : Action to perform (import/export/clear-cache)}
                          {--locale= : Specific locale to process}
                          {--group= : Specific translation group to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage application translations';

    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        parent::__construct();
        $this->translationService = $translationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $locale = $this->option('locale');
        $group = $this->option('group');

        switch ($action) {
            case 'import':
                $this->importTranslations();
                break;
            case 'export':
                $this->exportTranslations($locale, $group);
                break;
            case 'clear-cache':
                $this->clearCache();
                break;
            default:
                $this->error("Invalid action. Use 'import', 'export', or 'clear-cache'");
                return 1;
        }

        return 0;
    }

    protected function importTranslations(): void
    {
        $this->info('Importing translations from language files...');
        $this->translationService->importFromFiles();
        $this->info('Translations imported successfully!');
    }

    protected function exportTranslations(?string $locale = null, ?string $group = null): void
    {
        $this->info('Exporting translations...');

        $query = Translation::query();

        if ($locale) {
            // Filter by locale
            $query->whereRaw("JSON_EXTRACT(text, '$.{$locale}') IS NOT NULL");
        }

        if ($group) {
            $query->where('group', $group);
        }

        $translations = $query->get();

        foreach ($translations as $translation) {
            $this->line("Key: {$translation->key}");
            $this->line("Group: {$translation->group}");
            $this->line("Translations: " . json_encode($translation->text, JSON_PRETTY_PRINT));
            $this->line('---');
        }

        $this->info('Export complete!');
    }

    protected function clearCache(): void
    {
        $this->info('Clearing translation cache...');
        $this->translationService->clearCache();
        $this->info('Translation cache cleared successfully!');
    }
}
