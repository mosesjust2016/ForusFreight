<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Console\Command;

class RepairShipmentDataFromSource extends Command
{
    protected $signature = 'shipments:repair-from-source {--dry-run : Report what would change without saving anything}';

    protected $description = 'Correct the 38 shipments hit by the bulk-import column-shift bug using the real source data recovered by the client, and add the one shipment missing entirely';

    /**
     * Recovered from the client's original tracking records. Each row
     * corresponds 1:1 (in order) to the 38 shipments already in the
     * database plus one (Womba Kadimba) that was never created.
     *
     * tracking/serial/code/cbm/eta/load_date/shipping/transit_port/phone
     * are null where the source genuinely has no value yet (shown as
     * "—") — most commonly for shipments still at "Order Placed" that
     * haven't been assigned a real tracking number by the carrier.
     */
    private const ROWS = [
        ['client' => 'James Yombwe', 'tracking' => 'SF0229977546080', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'General Cargo', 'weight' => 2, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260961016436'],
        ['client' => 'Jeff Kasola', 'tracking' => '703003614399', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Water Pump', 'weight' => 13, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260966653306'],
        ['client' => 'Jericho Sakala', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Mobile Phone Battery', 'weight' => 46.2, 'qty' => 2, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260976225637'],
        ['client' => 'Kelvin Mwansa', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Auto Parts', 'weight' => 32.5, 'qty' => 3, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260968731266'],
        ['client' => 'Kelvin Mwansa', 'tracking' => '910057256572', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Accessories', 'weight' => 23.6, 'qty' => 2, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260968731266'],
        ['client' => 'Kelvine Mwansa', 'tracking' => 'SF519406544', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'General Cargo', 'weight' => 34.1, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260968731266'],
        ['client' => 'Maureen Bbuku', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Television', 'weight' => 56.6, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260979851991'],
        ['client' => 'Maureen Bbuku', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Table', 'weight' => 0, 'qty' => 20, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '+260977363124'],
        ['client' => 'Maureen Bbuku', 'tracking' => null, 'serial' => 'RS.26060875', 'code' => 'ZMFFL 3124', 'origin_port' => null, 'cargo' => 'Chair', 'weight' => 1827, 'qty' => 63, 'cbm' => 23.94, 'status' => 'Ordered', 'load_date' => '08/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '+260977363124'],
        ['client' => 'Musonda RD', 'tracking' => 'Y2650807653', 'serial' => 'RS.26052178', 'code' => 'ZMFFL 1967', 'origin_port' => null, 'cargo' => 'Auto Parts', 'weight' => 669, 'qty' => 1, 'cbm' => 1.4596, 'status' => 'Ordered', 'load_date' => '26/05/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '260977871967'],
        ['client' => 'Paul', 'tracking' => null, 'serial' => 'RS.26060654', 'code' => 'ZMFFL 7763', 'origin_port' => null, 'cargo' => 'Plastic Crate, Backpack', 'weight' => 17.6, 'qty' => 2, 'cbm' => 0.1144, 'status' => 'Ordered', 'load_date' => '06/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '260977777763'],
        ['client' => 'Paul Tembo', 'tracking' => 'SF0215203406167', 'serial' => 'RS.26061159', 'code' => 'ZMFFL 7763', 'origin_port' => null, 'cargo' => 'Luggage', 'weight' => 31, 'qty' => 2, 'cbm' => 0.1779, 'status' => 'Ordered', 'load_date' => '11/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '260977777763'],
        ['client' => 'Royd Kabwela', 'tracking' => 'SF0210132486890', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Luggage', 'weight' => 9.8, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260962525001'],
        ['client' => 'Royd Kabwela', 'tracking' => 'JYM188057706950', 'serial' => 'RS.26061699', 'code' => 'ZMFFL 5001', 'origin_port' => null, 'cargo' => 'Accessories', 'weight' => 12, 'qty' => 1, 'cbm' => 0.07, 'status' => 'Ordered', 'load_date' => '15/06/2026', 'eta' => '24/08/2026', 'shipping' => 'Sea Freight', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '962525001'],
        ['client' => 'Sophie Mutale', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Refrigerator', 'weight' => 56.2, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '+260973284621'],
        ['client' => 'Tisa Jobo', 'tracking' => null, 'serial' => 'RS.26062362', 'code' => 'ZMFFL 6335', 'origin_port' => null, 'cargo' => 'Clothes', 'weight' => 264, 'qty' => 4, 'cbm' => 1.31, 'status' => 'Ordered', 'load_date' => '22/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '260972936355'],
        ['client' => 'Tongai Muvindi', 'tracking' => 'JT3164814721041', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Accessories', 'weight' => 0.01, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => '24/08/2026', 'shipping' => null, 'transit_port' => null, 'phone' => '+260970009656'],
        ['client' => 'Vincent', 'tracking' => '910058399135', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Midea Microwave Oven, Electric Fan', 'weight' => 18.2, 'qty' => 2, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '+260773951073'],
        ['client' => 'Zelipa Sakala', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Cloths', 'weight' => 624, 'qty' => 12, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260777156118'],
        ['client' => 'Mapenzi Chisanza', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Hardware', 'weight' => 52, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260977645809'],
        ['client' => 'Abel Kangasa', 'tracking' => null, 'serial' => 'RS.26052331', 'code' => 'ZMFFL', 'origin_port' => null, 'cargo' => 'Accessories, Switch/Cable Accessories', 'weight' => 230, 'qty' => 14, 'cbm' => 1.49, 'status' => 'Ordered', 'load_date' => '28/05/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => null],
        ['client' => 'Adrian Chunga', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Water-based Exterior Wall Paint, Primer, Tools, Putty Powder', 'weight' => 905, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260970026344'],
        ['client' => 'Adrian Chunga', 'tracking' => '610080707216', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Bathroom Supplies', 'weight' => 136, 'qty' => 3, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => '24/08/2026', 'shipping' => null, 'transit_port' => null, 'phone' => '260970026344'],
        ['client' => 'AnderShumba', 'tracking' => 'JYM188057084606', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Coil Kit, Pius Gasket', 'weight' => 26, 'qty' => 2, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260965895510'],
        ['client' => 'Boyd Lyandokela', 'tracking' => null, 'serial' => 'RS.26062371', 'code' => 'ZMFFL 1372', 'origin_port' => null, 'cargo' => 'Automobile Wheel Hub', 'weight' => 180, 'qty' => 16, 'cbm' => 1.256, 'status' => 'Ordered', 'load_date' => '22/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '+260975331372'],
        ['client' => 'Bwafwayo Kabwe', 'tracking' => '750106640264', 'serial' => 'RS.26061709', 'code' => 'ZMFFL 8118', 'origin_port' => null, 'cargo' => 'Electric Vehicle', 'weight' => 54, 'qty' => 1, 'cbm' => 0.3437, 'status' => 'Ordered', 'load_date' => '15/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '0977448118'],
        ['client' => 'Chinyama Grace', 'tracking' => null, 'serial' => 'RS.26061299', 'code' => 'ZMFFL 3650', 'origin_port' => null, 'cargo' => 'Lighting Fixture, Gas Stove, Laundry Basket, Artificial Flower', 'weight' => 135, 'qty' => 17, 'cbm' => 1.1363, 'status' => 'Ordered', 'load_date' => '12/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '260977633650'],
        ['client' => 'Chipesha Mulenga Chanda', 'tracking' => '73712816984521', 'serial' => 'RS.26061799', 'code' => 'ZMFFL 1457', 'origin_port' => null, 'cargo' => 'Automobile Hose', 'weight' => 0, 'qty' => 1, 'cbm' => 0, 'status' => 'Ordered', 'load_date' => '16/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '260966581457'],
        ['client' => 'Collins Mooya', 'tracking' => '773426749237147', 'serial' => 'RS.26061915', 'code' => 'ZMFFL 9235', 'origin_port' => null, 'cargo' => 'Glasses', 'weight' => 0, 'qty' => 1, 'cbm' => 0, 'status' => 'Ordered', 'load_date' => '17/06/2026', 'eta' => '24/08/2026', 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '0978559235'],
        ['client' => 'Emmanuel', 'tracking' => null, 'serial' => 'RS.26060577', 'code' => 'ZMFFL 7077', 'origin_port' => null, 'cargo' => 'Automobile Lamp', 'weight' => 5, 'qty' => 1, 'cbm' => 0.0369, 'status' => 'Ordered', 'load_date' => '06/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => null],
        ['client' => 'Emmanuel Phiri', 'tracking' => 'XF0059847752', 'serial' => 'RS.26052641', 'code' => 'ZMFFL 0401', 'origin_port' => null, 'cargo' => 'Alarm System', 'weight' => 3, 'qty' => 1, 'cbm' => 0.0131, 'status' => 'Ordered', 'load_date' => '31/05/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '260979400401'],
        ['client' => 'Esther Makala', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Clothes', 'weight' => 206, 'qty' => 2, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '+260978725940'],
        ['client' => 'Grace Milumbe', 'tracking' => null, 'serial' => 'RS.26061300', 'code' => 'ZMFFL 1991', 'origin_port' => null, 'cargo' => 'Gas Stove, Induction Cooker, Artificial Flower', 'weight' => 62, 'qty' => 8, 'cbm' => 0.51, 'status' => 'Ordered', 'load_date' => '12/06/2026', 'eta' => null, 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '260979851991'],
        ['client' => 'Innocent Zulu', 'tracking' => '700912765254', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'General Cargo', 'weight' => 14.1, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260973950691'],
        ['client' => 'Innova', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Mini Excavator, Tractor', 'weight' => 3000, 'qty' => 2, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260971557315'],
        ['client' => 'Isaac Vumbi', 'tracking' => '79012582741535', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Clothes', 'weight' => 40.8, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260970998611'],
        ['client' => 'Isaac Vumbi', 'tracking' => '79013518265791', 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Clothes', 'weight' => 10.4, 'qty' => 1, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => '260970998611'],
        ['client' => 'James Songwe', 'tracking' => null, 'serial' => null, 'code' => null, 'origin_port' => 'Guangzhou Port', 'cargo' => 'Building Materials', 'weight' => 7437, 'qty' => 241, 'cbm' => null, 'status' => 'Order Placed', 'load_date' => null, 'eta' => null, 'shipping' => null, 'transit_port' => null, 'phone' => null],
        ['client' => 'Womba Kadimba', 'tracking' => '773421428627451', 'serial' => 'RS.26060617', 'code' => 'ZMFFL 6982', 'origin_port' => null, 'cargo' => 'Toilet Cleaner', 'weight' => 4.6, 'qty' => 1, 'cbm' => 0.03, 'status' => 'Ordered', 'load_date' => '25/06/2026', 'eta' => '24/08/2026', 'shipping' => 'Sea', 'transit_port' => 'Port of Beira, Mozambique', 'phone' => '978776982'],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $prefix = $dryRun ? '[DRY RUN] ' : '';

        $updated = 0;
        $created = 0;
        $notFound = [];

        foreach (self::ROWS as $i => $row) {
            $rowNum = $i + 1;

            $shipment = $row['tracking']
                ? Shipment::where('tracking_number', $row['tracking'])->first()
                : Shipment::where('client_name', $row['client'])
                    ->where('destination', $row['cargo']) // pre-fix corruption put cargo type in destination
                    ->whereNull('tracking_number')
                    ->first();

            $attributes = [
                'client_name' => $row['client'],
                'tracking_number' => $row['tracking'],
                'code' => $row['code'],
                'origin' => 'China',
                'port_of_origin' => $row['origin_port'],
                'current_border' => $row['transit_port'],
                'destination' => 'Lusaka, Zambia',
                'description' => $row['cargo'],
                'shipping_method' => $row['shipping'],
                'status' => $row['status'],
                'date_of_load' => $this->parseDate($row['load_date']),
                'estimated_delivery' => $this->parseDate($row['eta']),
                'no_of_parcels' => $row['qty'],
                'quantity' => $row['qty'],
                'gross_weight' => $row['weight'],
                'weight' => $row['weight'],
                'cbm_volume' => $row['cbm'],
                'client_phone' => $row['phone'],
            ];
            if ($row['serial']) {
                $attributes['serial_no'] = $row['serial'];
            }

            if ($shipment) {
                $this->line(sprintf('  Row %2d  #%-4d %-25s MATCHED', $rowNum, $shipment->id, $row['client']));
                if (!$dryRun) {
                    $shipment->update($attributes);
                }
                $updated++;
                continue;
            }

            if (!$row['tracking']) {
                $this->error(sprintf('  Row %2d  %-25s NOT FOUND (no tracking number to match, and no destination match either)', $rowNum, $row['client']));
                $notFound[] = $row['client'];
                continue;
            }

            $this->line(sprintf('  Row %2d  %-25s CREATE (no existing shipment with this tracking number)', $rowNum, $row['client']));
            if (!$dryRun) {
                $user = User::where('name', $row['client'])->first() ?? User::create([
                    'name' => $row['client'],
                    'email' => null,
                    'password' => 'temp' . uniqid(),
                    'is_admin' => false,
                ]);

                $serial = $row['serial'];
                if ($serial) {
                    $base = $serial;
                    $n = 0;
                    while (Shipment::where('serial_no', $serial)->exists()) {
                        $n++;
                        $serial = $base . '-' . $n;
                    }
                } else {
                    do {
                        $serial = 'ZML-' . strtoupper(substr(uniqid(), -8));
                    } while (Shipment::where('serial_no', $serial)->exists());
                }

                Shipment::withoutEvents(fn () => Shipment::create(array_merge($attributes, [
                    'user_id' => $user->id,
                    'serial_no' => $serial,
                ])));
            }
            $created++;
        }

        $this->newLine();
        $this->info("{$prefix}Matched and updated: {$updated}");
        $this->info("{$prefix}Created new: {$created}");
        if (!empty($notFound)) {
            $this->warn('Could not match (needs manual review): ' . implode(', ', $notFound));
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('Dry run only — nothing was saved. Re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    private function parseDate(?string $date): ?\Carbon\Carbon
    {
        if (!$date) {
            return null;
        }

        return \Carbon\Carbon::createFromFormat('d/m/Y', $date)->startOfDay();
    }
}
