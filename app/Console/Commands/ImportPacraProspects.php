<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\PacraCompanyService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportPacraProspects extends Command
{
    protected $signature = 'pacra:import-prospects {--term=* : Additional PACRA search terms}';
    protected $description = 'Import freight-relevant Zambian companies from the public PACRA search endpoint';

    public function handle(PacraCompanyService $pacra): int
    {
        $terms = array_values(array_unique(array_filter(array_merge([
            'freight', 'transport', 'logistics', 'manufacturing', 'wholesale',
            'import', 'export', 'mining', 'agriculture', 'construction',
            'retail', 'pharmaceutical', 'engineering', 'distribution',
        ], $this->option('term')))));
        $created = 0;
        $updated = 0;
        $seen = [];
        $requestCount = 0;
        $requestLimit = max(1, (int) config('services.pacra.max_requests_per_run', 40));

        foreach ($terms as $term) {
            if ($requestCount >= $requestLimit) {
                $this->warn("PACRA request limit reached ({$requestLimit}); remaining terms will run on the next schedule.");
                break;
            }
            $requestCount++;
            try {
                $results = $pacra->search($term);
            } catch (\Throwable $exception) {
                $this->warn("PACRA search failed for [{$term}].");
                continue;
            }

            foreach (is_array($results) ? $results : [] as $record) {
                $number = trim((string) ($record['entityNumber'] ?? ''));
                $name = trim((string) ($record['entityName'] ?? ''));
                if ($name === '' || ($number !== '' && isset($seen[$number]))) continue;
                if ($number !== '') $seen[$number] = true;

                $company = $number !== ''
                    ? Company::where('pacra_entity_number', $number)->first()
                    : Company::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();
                $data = [
                    'name' => $name,
                    'industry' => $record['natureofBusiness'] ?? $record['isicDescription'] ?? null,
                    'country' => 'Zambia',
                    'status' => $company?->status === 'active' ? 'active' : 'prospect',
                    'pacra_entity_number' => $number ?: null,
                    'pacra_entity_status' => $record['entityStatus'] ?? null,
                    'pacra_business_nature' => $record['natureofBusiness'] ?? null,
                    'pacra_entity_id' => $record['entityID'] ?? null,
                    'pacra_registration_date' => $this->parseDate($record['registrationDate'] ?? null),
                    'pacra_entity_type' => isset($record['entityTypeId']) ? (string) $record['entityTypeId'] : null,
                    'pacra_company_type' => $record['companyType'] ?? null,
                    'pacra_entity_limit' => $record['entityLimit'] ?? null,
                    'pacra_isic_classification' => $record['isicClasification'] ?? null,
                    'pacra_isic_description' => $record['isicDescription'] ?? null,
                    'pacra_record' => $record,
                    'pacra_verified_at' => now(),
                    'notes' => 'Imported from PACRA public registry search. Review before outreach.',
                ];
                if ($company) { $company->update($data); $updated++; }
                else { Company::create($data); $created++; }
            }
        }

        $this->info("PACRA prospect import complete: {$created} added, {$updated} refreshed across {$requestCount} request(s).");
        return self::SUCCESS;
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) return null;
        try { return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d'); }
        catch (\Throwable) { return null; }
    }
}
