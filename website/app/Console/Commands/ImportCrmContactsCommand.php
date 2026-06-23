<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ImportCrmContactsCommand extends Command
{
    protected $signature = 'crm:import-contacts {file? : Path to CSV file}';
    protected $description = 'Import CRM contacts from CSV file';

    public function handle()
    {
        $file = $this->argument('file') ?? database_path('seeders/CrmContactsImport.csv');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Importing CRM contacts from: {$file}");

        try {
            $handle = fopen($file, 'r');
            if (!$handle) {
                $this->error("Cannot open file: {$file}");
                return 1;
            }

            $headers = fgetcsv($handle);
            $imported = 0;
            $skipped = 0;
            $errors = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($headers)) {
                    $skipped++;
                    continue;
                }
                $record = array_combine($headers, $row);

                try {
                    $phone = $this->formatPhone($record['Contact Detail']);

                    if (empty($record['Customer Name']) || empty($phone)) {
                        $skipped++;
                        continue;
                    }

                    $crm_status = $this->parseCrmStatus($record['Shipment Status']);
                    $date = $this->parseDate($record['Date']);

                    $user = User::firstOrCreate(
                        ['phone' => $phone],
                        [
                            'name' => $record['Customer Name'],
                            'email' => $this->generateEmail($record['Customer Name'], $phone),
                            'phone' => $phone,
                            'company_name' => $record['Business Name'] ?? null,
                            'crm_status' => $crm_status,
                            'password' => Hash::make('temp_' . $phone),
                            'email_verified_at' => now(),
                            'phone_verified_at' => now(),
                            'internal_notes' => $this->formatNotes($record),
                        ]
                    );

                    if ($user->wasRecentlyCreated) {
                        $imported++;
                        $this->line("✓ Created: {$record['Customer Name']} ({$phone})");
                    } else {
                        $this->line("→ Exists: {$record['Customer Name']} ({$phone})");
                    }

                } catch (\Exception $e) {
                    $errors++;
                    $this->warn("✗ Error importing {$record['Customer Name']}: {$e->getMessage()}");
                }
            }

            fclose($handle);

            $this->info("\n✅ Import Complete!");
            $this->line("Imported: $imported | Skipped: $skipped | Errors: $errors");

            return 0;

        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            return 1;
        }
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (empty($phone)) {
            return '';
        }

        if (strlen($phone) === 9) {
            $phone = '260' . $phone;
        }

        if (strlen($phone) === 10) {
            $phone = '260' . substr($phone, 1);
        }

        return '+' . $phone;
    }

    private function parseCrmStatus(string $status): string
    {
        $status = strtolower(trim($status));

        if (str_contains($status, 'lead')) {
            return 'lead';
        }
        if (str_contains($status, 'closed')) {
            return 'closed';
        }
        if (str_contains($status, 'prospect')) {
            return 'prospect';
        }

        return 'lead';
    }

    private function parseDate(string $date): ?\DateTime
    {
        try {
            $date = trim($date);
            if (empty($date)) {
                return null;
            }

            $formats = ['d/m/Y', 'd-m-Y', 'm/d/Y'];
            foreach ($formats as $format) {
                $parsed = \DateTime::createFromFormat($format, $date);
                if ($parsed !== false) {
                    return $parsed;
                }
            }
        } catch (\Exception $e) {
            // Return null if unable to parse
        }

        return null;
    }

    private function generateEmail(string $name, string $phone): string
    {
        $nameParts = explode(' ', strtolower($name));
        $baseName = $nameParts[0] ?? 'contact';
        $phoneDigits = substr($phone, -7);

        return "{$baseName}_{$phoneDigits}@forusflco.local";
    }

    private function formatNotes(array $record): string
    {
        $notes = [];

        if (!empty($record['Business Name'])) {
            $notes[] = "Business: {$record['Business Name']}";
        }

        if (!empty($record['Shipment Status'])) {
            $notes[] = "Status: {$record['Shipment Status']}";
        }

        if (!empty($record['Follow up Date'])) {
            $notes[] = "Follow up: {$record['Follow up Date']}";
        }

        if (!empty($record['Comments'])) {
            $notes[] = "Notes: {$record['Comments']}";
        }

        if (!empty($record['Date'])) {
            $notes[] = "Added: {$record['Date']}";
        }

        return implode(" | ", $notes);
    }
}
