<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InternalDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $office = Office::where('code', 'PICTO')->first();

        if (!$office) {
            $this->command?->warn('PICTO office not found. Run OfficesSeeder first.');
            return;
        }

        $user = User::where('office_id', $office->id)->orderBy('id')->first() ?? User::orderBy('id')->first();

        foreach ($this->legacyRows() as $row) {
            $normalizedCode = $this->normalizeCode($row['code']);
            $dateReceived = $this->parseDate($row['date']);
            $status = $this->normalizeStatus($row['status']);
            $documentType = $this->normalizeDocumentType($row['type']);
            $subject = trim((string) $row['subject']) !== ''
                ? trim((string) $row['subject'])
                : 'Legacy internal memorandum (subject missing)';

            $remarks = $this->buildRemarks($row['shared_drive_link']);

            $document = Document::updateOrCreate(
                ['dts_number' => $normalizedCode],
                [
                    'picto_number' => null,
                    'doc_number' => $normalizedCode,
                    'memorandum_number' => null,
                    'period' => null,
                    'particulars' => $this->nullableTrim($row['particulars']),
                    'document_type' => $documentType,
                    'direction' => 'OUTGOING',
                    'delivery_scope' => 'INTERNAL',
                    'originating_office' => $office->id,
                    'to_office' => $office->id,
                    'current_office' => $office->id,
                    'current_holder' => $user?->id,
                    'subject' => $subject,
                    'action_required' => null,
                    'endorsed_to' => null,
                    'date_received' => $dateReceived,
                    'status' => $status,
                    'remarks' => $remarks,
                    'shared_drive_link' => $this->extractUrl($row['shared_drive_link']),
                    'received_via_online' => false,
                    'encoded_by' => $user?->id,
                    'opg_reference_no' => null,
                    'opa_reference_no' => null,
                    'governors_instruction' => null,
                    'administrators_instruction' => null,
                    'returned' => null,
                    'opg_action_slip' => null,
                    'dts_no' => null,
                ]
            );

            if (!$document->routes()->exists()) {
                $routeTimestamp = $dateReceived ? Carbon::parse($dateReceived)->startOfDay() : now();

                $document->routes()->create([
                    'from_office' => $office->id,
                    'to_office' => $office->id,
                    'released_by' => $user?->id,
                    'received_by' => $user?->id,
                    'datetime_released' => $routeTimestamp,
                    'datetime_received' => $status === 'ONGOING' ? null : $routeTimestamp,
                    'remarks' => 'Imported from legacy internal memo/SO register',
                ]);
            }
        }

        $this->command?->info('Legacy internal memo/SO records imported successfully.');
    }

    private function normalizeCode(string $code): string
    {
        return preg_replace('/^ICTU/i', 'PICTO', trim($code));
    }

    private function normalizeDocumentType(string $type): string
    {
        $normalized = strtoupper(trim($type));

        return str_contains($normalized, 'SPECIAL ORDER') ? 'SO' : 'MEMO';
    }

    private function normalizeStatus(string $status): string
    {
        return strtoupper(trim($status)) === 'ONGOING' ? 'ONGOING' : 'COMPLETED';
    }

    private function parseDate(?string $date): ?string
    {
        $date = trim((string) $date);

        if ($date === '') {
            return null;
        }

        return Carbon::parse($date)->format('Y-m-d');
    }

    private function extractUrl(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return preg_match('/^https?:\/\//i', $value) ? $value : null;
    }

    private function buildRemarks(?string $sharedDriveLink): ?string
    {
        $sharedDriveLink = trim((string) $sharedDriveLink);

        if ($sharedDriveLink === '' || preg_match('/^https?:\/\//i', $sharedDriveLink)) {
            return null;
        }

        return 'Legacy source file: ' . $sharedDriveLink;
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function legacyRows(): array
    {
        return [
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0001', 'type' => 'MEMORANDUM', 'date' => '5/10/2024', 'subject' => 'Commendation for the month of April Best in Office Attendance', 'particulars' => 'MAdeguzman, JRMifa,JMarquez', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0002', 'type' => 'MEMORANDUM', 'date' => '6/11/2024', 'subject' => 'Commendation for the month of May Best in Office Attendance', 'particulars' => 'CRPicardal, MJEmeterio', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0003', 'type' => 'MEMORANDUM', 'date' => '7/1/2024', 'subject' => 'Commendation for the month of June Best in Office Attendance', 'particulars' => 'TJApigo, MAdeguzman, RGDumaguin', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0004', 'type' => 'MEMORANDUM', 'date' => '8/20/2024', 'subject' => 'Commendation for the month of July Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0005', 'type' => 'MEMORANDUM', 'date' => '9/6/2024', 'subject' => 'Commendation for the month of August Best in Office Attendance', 'particulars' => 'TJApigo', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0006', 'type' => 'MEMORANDUM', 'date' => '9/6/2024', 'subject' => 'Commendation for the month of August Best in Office Attendance', 'particulars' => 'JABalanon', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0007', 'type' => 'MEMORANDUM', 'date' => '9/6/2024', 'subject' => 'Commendation for the month of August Best in Office Attendance', 'particulars' => 'MADeGuzman', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0008', 'type' => 'MEMORANDUM', 'date' => '9/6/2024', 'subject' => 'Commendation for the month of August Best in Office Attendance', 'particulars' => 'JRMifa', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0009', 'type' => 'MEMORANDUM', 'date' => '9/6/2024', 'subject' => 'Commendation for the month of August Best in Office Attendance', 'particulars' => 'JDucusin', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0010', 'type' => 'MEMORANDUM', 'date' => '', 'subject' => '', 'particulars' => '', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0011', 'type' => 'MEMORANDUM', 'date' => '9/25/2024', 'subject' => 'Tardiness for the month of August', 'particulars' => 'AMAquino', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0012', 'type' => 'MEMORANDUM', 'date' => '10/4/2024', 'subject' => 'Commendation for the month of September Best in Office Attendance', 'particulars' => 'MJEmeterio', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0013', 'type' => 'MEMORANDUM', 'date' => '10/4/2024', 'subject' => 'Commendation for the month of September Best in Office Attendance', 'particulars' => 'MAOfiaza', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0014', 'type' => 'MEMORANDUM', 'date' => '11/5/2024', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'JMarquez', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0015', 'type' => 'MEMORANDUM', 'date' => '11/5/2024', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'MADeGuzman', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0016', 'type' => 'MEMORANDUM', 'date' => '12/2/2024', 'subject' => 'Commendation for the month of November Best in Office Attendance', 'particulars' => 'MADeGuzman', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-0017', 'type' => 'MEMORANDUM', 'date' => '12/2/2024', 'subject' => 'Commendation for the month of November Best in Office Attendance', 'particulars' => 'CRPicardal', 'status' => 'Done'],

            ['shared_drive_link' => 'IH_Letter Designation as Official Driver.PDF', 'code' => 'ICTU-INDOC-2025-0001', 'type' => 'Special Order', 'date' => '1/27/2025', 'subject' => 'Designation as Official Driver', 'particulars' => 'AMAquino', 'status' => 'Done'],
            ['shared_drive_link' => 'IH_Letter Designation as in Charge of Office .PDF', 'code' => 'ICTU-INDOC-2025-0002', 'type' => 'Special Order', 'date' => '1/27/2025', 'subject' => 'Designation as in Charge of Office', 'particulars' => 'MEEspiritu', 'status' => 'Done'],
            ['shared_drive_link' => 'IH_Letter Reiteration on the Utilization of the Service Vehicle.PDF', 'code' => 'ICTU-INDOC-2025-0003', 'type' => 'Memorandum', 'date' => '1/27/2025', 'subject' => 'Reiteration on the Utilization the Service Vehicle', 'particulars' => 'ICTU Staff', 'status' => 'Done'],
            ['shared_drive_link' => 'SO No. 3 Designation as ICT Infrastructure and Network Management Section.PDF', 'code' => 'ICTU-INDOC-2025-0004', 'type' => 'Special Order', 'date' => '1/28/2025', 'subject' => 'Designation as ICT Infrastructure and Network Management Section (IINMU) Section Head of the OPGICTU', 'particulars' => 'TJApigo', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0005,0006,007.PDF', 'code' => 'ICTU-INDOC-2025-0005', 'type' => 'Memorandum', 'date' => '3/3/2025', 'subject' => 'Commendation for the month of February Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0005,0006,007.PDF', 'code' => 'ICTU-INDOC-2025-0006', 'type' => 'Memorandum', 'date' => '3/3/2025', 'subject' => 'Commendation for the month of February Best in Office Attendance', 'particulars' => 'MADeGuzman', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0005,0006,007.PDF', 'code' => 'ICTU-INDOC-2025-0007', 'type' => 'Memorandum', 'date' => '3/3/2025', 'subject' => 'Tardiness for the month of February', 'particulars' => 'MJPatacsil', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0008 Participation in the Grand Peoples Parade.PDF', 'code' => 'ICTU-INDOC-2025-0008', 'type' => 'Memorandum', 'date' => '3/11/2025', 'subject' => 'Participation in Grand Peoples Parade March14, 2025', 'particulars' => 'EDLiwanag, JABalanon, JPBalanon, JLNiclas', 'status' => 'Done'],
            ['shared_drive_link' => 'TBallesteros Commendation.PDF', 'code' => 'ICTU-INDOC-2025-0009', 'type' => 'Memorandum', 'date' => '4/2/2025', 'subject' => 'Commendation for the month of March Best in Office Attendance', 'particulars' => 'TIBallesteros', 'status' => 'Done'],
            ['shared_drive_link' => 'ELiwanag Commendation.PDF', 'code' => 'ICTU-INDOC-2025-0010', 'type' => 'Memorandum', 'date' => '4/2/2025', 'subject' => 'Commendation for the month of March Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Done'],
            ['shared_drive_link' => 'DFlorendo Commendation.PDF', 'code' => 'ICTU-INDOC-2025-0011', 'type' => 'Memorandum', 'date' => '4/2/2025', 'subject' => 'Commendation for the month of March Best in Office Attendance', 'particulars' => 'DRFlorendo', 'status' => 'Done'],
            ['shared_drive_link' => 'CPicardal Commendation.PDF', 'code' => 'ICTU-INDOC-2025-0012', 'type' => 'Memorandum', 'date' => '4/2/2025', 'subject' => 'Commendation for the month of March Best in Office Attendance', 'particulars' => 'CRPicardal', 'status' => 'Done'],
            ['shared_drive_link' => 'MADeGuzman Commendation.PDF', 'code' => 'ICTU-INDOC-2025-0013', 'type' => 'Memorandum', 'date' => '4/2/2025', 'subject' => 'Commendation for the month of March Best in Office Attendance', 'particulars' => 'MADeGuzman', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0014 EDLiwanag.PDF', 'code' => 'ICTU-INDOC-2025-0014', 'type' => 'Memorandum', 'date' => '4/30/2025', 'subject' => 'Commendation for the month of April Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0015 MADeGuzman.PDF', 'code' => 'ICTU-INDOC-2025-0015', 'type' => 'Memorandum', 'date' => '4/30/2025', 'subject' => 'Commendation for the month of April Best in Office Attendance', 'particulars' => 'MADeGuzman', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0016.PDF', 'code' => 'ICTU-INDOC-2025-0016', 'type' => 'Memorandum', 'date' => '5/26/2025', 'subject' => 'Designation as in Charge of Office May 28, 2025', 'particulars' => 'MEEspiritu', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0017.PDF', 'code' => 'ICTU-INDOC-2025-0017', 'type' => 'Memorandum', 'date' => '5/26/2025', 'subject' => 'Designation as in Charge of Office  June 3, 2025', 'particulars' => 'MEEspiritu', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0018.PDF', 'code' => 'ICTU-INDOC-2025-0018', 'type' => 'Memorandum', 'date' => '5/26/2025', 'subject' => 'Designation as in Charge of Office June 10, 2025', 'particulars' => 'MEEspiritu', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-2025-0019', 'type' => 'Memorandum', 'date' => '6/3/2025', 'subject' => 'Commendation for the month of May Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-2025-0020', 'type' => 'Memorandum', 'date' => '6/3/2025', 'subject' => 'Tardiness for the month of May 2025', 'particulars' => 'NCQuibin', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0021 Designation as OIC June 10-13, 2025.PDF', 'code' => 'ICTU-INDOC-2025-0021', 'type' => 'Memorandum', 'date' => '6/9/2025', 'subject' => 'Designation as in Charge of Office June 10-13, 2025', 'particulars' => 'MEEspiritu', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0022.PDF', 'code' => 'ICTU-INDOC-2025-0022', 'type' => 'Memorandum', 'date' => '7/1/2025', 'subject' => 'Commendation for the month of June Best in Office Attendance', 'particulars' => 'TIBallesteros', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0023.PDF', 'code' => 'ICTU-INDOC-2025-0023', 'type' => 'Memorandum', 'date' => '7/1/2025', 'subject' => 'Commendation for the month of June Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0024.PDF', 'code' => 'ICTU-INDOC-2025-0024', 'type' => 'Memorandum', 'date' => '7/1/2025', 'subject' => 'Commendation for the month of June Best in Office Attendance', 'particulars' => 'DRFlorendo', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0025.PDF', 'code' => 'ICTU-INDOC-2025-0025', 'type' => 'Memorandum', 'date' => '7/1/2025', 'subject' => 'Commendation for the month of June Best in Office Attendance', 'particulars' => 'MADeGuzman', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0026 Margie Casilla.PDF', 'code' => 'ICTU-INDOC-2025-0026', 'type' => 'Memorandum', 'date' => '08/06/2025', 'subject' => 'Tardiness for the month of July 2025', 'particulars' => 'MGCasilla', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0027 Albert Aquino.PDF', 'code' => 'ICTU-INDOC-2025-0027', 'type' => 'Memorandum', 'date' => '08/06/2025', 'subject' => 'Tardiness for the month of July 2025', 'particulars' => 'AMAquino', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0028 Maria Elena Espiritu.PDF', 'code' => 'ICTU-INDOC-2025-0028', 'type' => 'Memorandum', 'date' => '08/07/2025', 'subject' => 'Designation as in Charge of Office Aug 13-15, 2025', 'particulars' => 'MEEspiritu', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0029 Designation as in Charge of Office Aug 20, 2025.pdf', 'code' => 'ICTU-INDOC-2025-0029', 'type' => 'Memorandum', 'date' => '08/20/2025', 'subject' => 'Designation as in Charge of Office Aug 20, 2025', 'particulars' => 'MEEspiritu', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0030 Tardiness for the month of August 2025.pdf', 'code' => 'ICTU-INDOC-2025-0030', 'type' => 'Memorandum', 'date' => '09/09/2025', 'subject' => 'Tardiness for the month of August 2025', 'particulars' => 'AMAquino', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-2025-0031', 'type' => 'Memorandum', 'date' => '09/19/2025', 'subject' => 'Commendation for the month of August Best in Office Attendance', 'particulars' => 'CRPicardal', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0032.pdf', 'code' => 'ICTU-INDOC-2025-0032', 'type' => 'Memorandum', 'date' => '10/01/2025', 'subject' => 'Tardiness for the month of September 2025', 'particulars' => 'MGCasilla', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0033 TJA Designation as in Charge of Office  October 1-3, 2025.pdf', 'code' => 'ICTU-INDOC-2025-0033', 'type' => 'Memorandum', 'date' => '10/01/2024', 'subject' => 'Designation as in Charge of Office  October 1-3, 2025', 'particulars' => 'TJApigo', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0034.pdf', 'code' => 'ICTU-INDOC-2025-0034', 'type' => 'Memorandum', 'date' => '10/10/2025', 'subject' => 'Commendation for the month of September Best in Office Attendance', 'particulars' => 'JRMifa', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0035.pdf', 'code' => 'ICTU-INDOC-2025-0035', 'type' => 'Memorandum', 'date' => '10/10/2025', 'subject' => 'Commendation for the month of September Best in Office Attendance', 'particulars' => 'TIBallesteros', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0036.pdf', 'code' => 'ICTU-INDOC-2025-0036', 'type' => 'Memorandum', 'date' => '10/10/2025', 'subject' => 'Commendation for the month of September Best in Office Attendance', 'particulars' => 'DRFlorendo', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0037.pdf', 'code' => 'ICTU-INDOC-2025-0037', 'type' => 'Memorandum', 'date' => '10/10/2025', 'subject' => 'Commendation for the month of September Best in Office Attendance', 'particulars' => 'MSCacdac', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0038.pdf', 'code' => 'ICTU-INDOC-2025-0038', 'type' => 'Memorandum', 'date' => '10/10/2025', 'subject' => 'Commendation for the month of September Best in Office Attendance', 'particulars' => 'MEEspiritu', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0039.pdf', 'code' => 'ICTU-INDOC-2025-0039', 'type' => 'Memorandum', 'date' => '11/03/2025', 'subject' => 'Tardiness for the month of October 2025', 'particulars' => 'MGCasilla', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0040.pdf', 'code' => 'ICTU-INDOC-2025-0040', 'type' => 'Memorandum', 'date' => '11/03/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'TIBallesteros', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0041.pdf', 'code' => 'ICTU-INDOC-2025-0041', 'type' => 'Memorandum', 'date' => '11/03/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'DRFlorendo', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0042.pdf', 'code' => 'ICTU-INDOC-2025-0042', 'type' => 'Memorandum', 'date' => '11/03/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'MAAEbreo', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0043.pdf', 'code' => 'ICTU-INDOC-2025-0043', 'type' => 'Memorandum', 'date' => '11/03/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0044.pdf', 'code' => 'ICTU-INDOC-2025-0044', 'type' => 'Memorandum', 'date' => '11/03/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'MADeGuzman', 'status' => 'Done'],
            ['shared_drive_link' => 'ICTU-INDOC-2025-0044.pdf', 'code' => 'ICTU-INDOC-2025-0045', 'type' => 'Memorandum', 'date' => '11/03/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'JPMarquez', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-2025-0046', 'type' => 'Memorandum', 'date' => '12/04/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Ongoing'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-2025-0047', 'type' => 'Memorandum', 'date' => '12/04/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'MAOfiaza', 'status' => 'Ongoing'],
            ['shared_drive_link' => '', 'code' => 'ICTU-INDOC-2025-0048', 'type' => 'Memorandum', 'date' => '12/04/2025', 'subject' => 'Commendation for the month of October Best in Office Attendance', 'particulars' => 'JPMarquez', 'status' => 'Ongoing'],

            ['shared_drive_link' => 'PICTO-INDOC-2026-0001.pdf', 'code' => 'PICTO-INDOC-2026-0001', 'type' => 'Memorandum', 'date' => '01/21/2026', 'subject' => 'Designation as Focal Person for Centralized Certificate Printing Services', 'particulars' => 'DLLibadia', 'status' => 'Done'],
            ['shared_drive_link' => '', 'code' => 'PICTO-INDOC-2026-0002', 'type' => 'Memorandum', 'date' => '02/02/2026', 'subject' => 'Tardiness for the month of January 2026', 'particulars' => 'MGCasilla', 'status' => 'Done'],
            ['shared_drive_link' => 'PICTO-INDOC-2026-0003.pdf', 'code' => 'PICTO-INDOC-2026-0003', 'type' => 'Memorandum', 'date' => '02/05/2026', 'subject' => 'Commendation for the month of January Best in Office Attendance', 'particulars' => 'MSCacdac', 'status' => 'Done'],
            ['shared_drive_link' => 'PICTO-INDOC-2026-0004.pdf', 'code' => 'PICTO-INDOC-2026-0004', 'type' => 'Memorandum', 'date' => '02/05/2026', 'subject' => 'Commendation for the month of January Best in Office Attendance', 'particulars' => 'JGDucusin', 'status' => 'Done'],
            ['shared_drive_link' => 'PICTO-INDOC-2026-0005.pdf', 'code' => 'PICTO-INDOC-2026-0005', 'type' => 'Memorandum', 'date' => '02/05/2026', 'subject' => 'Commendation for the month of January Best in Office Attendance', 'particulars' => 'EDLiwanag', 'status' => 'Done'],
            ['shared_drive_link' => 'PICTO-INDOC-2026-0006.pdf', 'code' => 'PICTO-INDOC-2026-0006', 'type' => 'Memorandum', 'date' => '02/05/2026', 'subject' => 'Commendation for the month of January Best in Office Attendance', 'particulars' => 'JVNToribio', 'status' => 'Done'],
        ];
    }
}
