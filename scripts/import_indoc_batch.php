<?php

declare(strict_types=1);

$lines = [
    "\tICTU-INDOC-0001\tMEMORANDUM\t5/10/2024\tCommendation for the month of April Best in Office Attendance\tMAdeguzman, JRMifa,JMarquez\tDone",
    "\tICTU-INDOC-0002\tMEMORANDUM\t6/11/2024\tCommendation for the month of May Best in Office Attendance\tCRPicardal, MJEmeterio\tDone",
    "\tICTU-INDOC-0003\tMEMORANDUM\t7/1/2024\tCommendation for the month of June Best in Office Attendance\tTJApigo, MAdeguzman, RGDumaguin\tDone",
    "\tICTU-INDOC-0004\tMEMORANDUM\t8/20/2024\tCommendation for the month of July Best in Office Attendance\tEDLiwanag\tDone",
    "\tICTU-INDOC-0005\tMEMORANDUM\t9/6/2024\tCommendation for the month of August Best in Office Attendance\tTJApigo\tDone",
    "\tICTU-INDOC-0006\tMEMORANDUM\t9/6/2024\tCommendation for the month of August Best in Office Attendance\tJABalanon\tDone",
    "\tICTU-INDOC-0007\tMEMORANDUM\t9/6/2024\tCommendation for the month of August Best in Office Attendance\tMADeGuzman\tDone",
    "\tICTU-INDOC-0008\tMEMORANDUM\t9/6/2024\tCommendation for the month of August Best in Office Attendance\tJRMifa\tDone",
    "\tICTU-INDOC-0009\tMEMORANDUM\t9/6/2024\tCommendation for the month of August Best in Office Attendance\tJDucusin\tDone",
    "\tICTU-INDOC-0010\tMEMORANDUM\t\t\t\tDone",
    "\tICTU-INDOC-0011\tMEMORANDUM\t9/25/2024\tTardiness for the month of August \tAMAquino\tDone",
    "\tICTU-INDOC-0012\tMEMORANDUM\t10/4/2024\tCommendation for the month of September Best in Office Attendance\tMJEmeterio\tDone",
    "\tICTU-INDOC-0013\tMEMORANDUM\t10/4/2024\tCommendation for the month of September Best in Office Attendance\tMAOfiaza\tDone",
    "\tICTU-INDOC-0014\tMEMORANDUM\t11/5/2024\tCommendation for the month of October Best in Office Attendance\tJMarquez\tDone",
    "\tICTU-INDOC-0015\tMEMORANDUM\t11/5/2024\tCommendation for the month of October Best in Office Attendance\tMADeGuzman\tDone",
    "\tICTU-INDOC-0016\tMEMORANDUM\t12/2/2024\tCommendation for the month of November Best in Office Attendance\tMADeGuzman\tDone",
    "\tICTU-INDOC-0017\tMEMORANDUM\t12/2/2024\tCommendation for the month of November Best in Office Attendance\tCRPicardal\tDone",
    "IH_Letter Designation as Official Driver.PDF\tICTU-INDOC-2025-0001\tSpecial Order\t1/27/2025\tDesignation as Official Driver\tAMAquino\tDone",
    "IH_Letter Designation as in Charge of Office .PDF\tICTU-INDOC-2025-0002\tSpecial Order\t1/27/2025\tDesignation as in Charge of Office\tMEEspiritu\tDone",
    "IH_Letter Reiteration on the Utilization of the Service Vehicle.PDF\tICTU-INDOC-2025-0003\tMemorandum\t1/27/2025\tReiteration on the Utilization the Service Vehicle \tICTU Staff\tDone",
    "SO No. 3 Designation as ICT Infrastructure and Network Management Section.PDF\tICTU-INDOC-2025-0004\tSpecial Order\t1/28/2025\tDesignation as ICT Infrastructure and Network Management Section (IINMU) Section Head of the OPGICTU\tTJApigo\tDone",
    "ICTU-INDOC-2025-0005,0006,007.PDF\tICTU-INDOC-2025-0005\tMemorandum\t3/3/2025\tCommendation for the month of February Best in Office Attendance\tEDLiwanag\tDone",
    "ICTU-INDOC-2025-0005,0006,007.PDF\tICTU-INDOC-2025-0006\tMemorandum\t3/3/2025\tCommendation for the month of February Best in Office Attendance\tMADeGuzman\tDone",
    "ICTU-INDOC-2025-0005,0006,007.PDF\tICTU-INDOC-2025-0007\tMemorandum\t3/3/2025\tTardiness for the month of February\tMJPatacsil\tDone",
    "ICTU-INDOC-2025-0008 Participation in the Grand Peoples Parade.PDF\tICTU-INDOC-2025-0008\tMemorandum\t3/11/2025\tParticipation in Grand Peoples Parade March14, 2025\tEDLiwanag, JABalanon, JPBalanon, JLNiclas\tDone",
    "TBallesteros Commendation.PDF\tICTU-INDOC-2025-0009\tMemorandum\t4/2/2025\tCommendation for the month of March Best in Office Attendance\tTIBallesteros\tDone",
    "ELiwanag Commendation.PDF\tICTU-INDOC-2025-0010\tMemorandum\t4/2/2025\tCommendation for the month of March Best in Office Attendance\tEDLiwanag\tDone",
    "DFlorendo Commendation.PDF\tICTU-INDOC-2025-0011\tMemorandum\t4/2/2025\tCommendation for the month of March Best in Office Attendance\tDRFlorendo\tDone",
    "CPicardal Commendation.PDF\tICTU-INDOC-2025-0012\tMemorandum\t4/2/2025\tCommendation for the month of March Best in Office Attendance\tCRPicardal\tDone",
    "MADeGuzman Commendation.PDF\tICTU-INDOC-2025-0013\tMemorandum\t4/2/2025\tCommendation for the month of March Best in Office Attendance\tMADeGuzman\tDone",
    "ICTU-INDOC-2025-0014 EDLiwanag.PDF\tICTU-INDOC-2025-0014\tMemorandum\t4/30/2025\tCommendation for the month of April Best in Office Attendance\tEDLiwanag\tDone",
    "ICTU-INDOC-2025-0015 MADeGuzman.PDF\tICTU-INDOC-2025-0015\tMemorandum\t4/30/2025\tCommendation for the month of April Best in Office Attendance\tMADeGuzman\tDone",
    "ICTU-INDOC-2025-0016.PDF\tICTU-INDOC-2025-0016\tMemorandum\t5/26/2025\tDesignation as in Charge of Office May 28, 2025\tMEEspiritu\tDone",
    "ICTU-INDOC-2025-0017.PDF\tICTU-INDOC-2025-0017\tMemorandum\t5/26/2025\tDesignation as in Charge of Office  June 3, 2025\tMEEspiritu\tDone",
    "ICTU-INDOC-2025-0018.PDF\tICTU-INDOC-2025-0018\tMemorandum\t5/26/2025\tDesignation as in Charge of Office June 10, 2025\tMEEspiritu\tDone",
    "\tICTU-INDOC-2025-0019\tMemorandum\t6/3/2025\tCommendation for the month of May Best in Office Attendance\tEDLiwanag\tDone",
    "\tICTU-INDOC-2025-0020\tMemorandum\t6/3/2025\tTardiness for the month of May 2025\tNCQuibin\tDone",
    "ICTU-INDOC-2025-0021 Designation as OIC June 10-13, 2025.PDF\tICTU-INDOC-2025-0021\tMemorandum\t6/9/2025\tDesignation as in Charge of Office June 10-13, 2025\tMEEspiritu\tDone",
    "ICTU-INDOC-2025-0022.PDF\tICTU-INDOC-2025-0022\tMemorandum\t7/1/2025\tCommendation for the month of June Best in Office Attendance\tTIBallesteros\tDone",
    "ICTU-INDOC-2025-0023.PDF\tICTU-INDOC-2025-0023\tMemorandum\t7/1/2025\tCommendation for the month of June Best in Office Attendance\tEDLiwanag\tDone",
    "ICTU-INDOC-2025-0024.PDF\tICTU-INDOC-2025-0024\tMemorandum\t7/1/2025\tCommendation for the month of June Best in Office Attendance\tDRFlorendo\tDone",
    "ICTU-INDOC-2025-0025.PDF\tICTU-INDOC-2025-0025\tMemorandum\t7/1/2025\tCommendation for the month of June Best in Office Attendance\tMADeGuzman\tDone",
    "ICTU-INDOC-2025-0026 Margie Casilla.PDF\tICTU-INDOC-2025-0026\tMemorandum\t08/06/2025\tTardiness for the month of July 2025\tMGCasilla\tDone",
    "ICTU-INDOC-2025-0027 Albert Aquino.PDF\tICTU-INDOC-2025-0027\tMemorandum\t08/06/2025\tTardiness for the month of July 2025\tAMAquino\tDone",
    "ICTU-INDOC-2025-0028 Maria Elena Espiritu.PDF\tICTU-INDOC-2025-0028\tMemorandum\t08/07/2025\tDesignation as in Charge of Office Aug 13-15, 2025\tMEEspiritu\tDone",
    "ICTU-INDOC-2025-0029 Designation as in Charge of Office Aug 20, 2025.pdf\tICTU-INDOC-2025-0029\tMemorandum\t08/20/2025\tDesignation as in Charge of Office Aug 20, 2025\tMEEspiritu\tDone",
    "ICTU-INDOC-2025-0030 Tardiness for the month of August 2025.pdf\tICTU-INDOC-2025-0030\tMemorandum\t09/09/2025\tTardiness for the month of August 2025\tAMAquino\tDone",
    "\tICTU-INDOC-2025-0031\tMemorandum\t09/19/2025\tCommendation for the month of August Best in Office Attendance\tCRPicardal\tDone",
    "ICTU-INDOC-2025-0032.pdf\tICTU-INDOC-2025-0032\tMemorandum\t10/01/2025\tTardiness for the month of September 2025\tMGCasilla\tDone",
    "ICTU-INDOC-2025-0033 TJA Designation as in Charge of Office  October 1-3, 2025.pdf\tICTU-INDOC-2025-0033\tMemorandum\t10/01/2024\tDesignation as in Charge of Office  October 1-3, 2025\tTJApigo\tDone",
    "ICTU-INDOC-2025-0034.pdf\tICTU-INDOC-2025-0034\tMemorandum\t10/10/2025\tCommendation for the month of September Best in Office Attendance\tJRMifa\tDone",
    "ICTU-INDOC-2025-0035.pdf\tICTU-INDOC-2025-0035\tMemorandum\t10/10/2025\tCommendation for the month of September Best in Office Attendance\tTIBallesteros\tDone",
    "ICTU-INDOC-2025-0036.pdf\tICTU-INDOC-2025-0036\tMemorandum\t10/10/2025\tCommendation for the month of September Best in Office Attendance\tDRFlorendo\tDone",
    "ICTU-INDOC-2025-0037.pdf\tICTU-INDOC-2025-0037\tMemorandum\t10/10/2025\tCommendation for the month of September Best in Office Attendance\tMSCacdac\tDone",
    "ICTU-INDOC-2025-0038.pdf\tICTU-INDOC-2025-0038\tMemorandum\t10/10/2025\tCommendation for the month of September Best in Office Attendance\tMEEspiritu\tDone",
    "ICTU-INDOC-2025-0039.pdf\tICTU-INDOC-2025-0039\tMemorandum\t11/03/2025\tTardiness for the month of October 2025\tMGCasilla\tDone",
    "ICTU-INDOC-2025-0040.pdf\tICTU-INDOC-2025-0040\tMemorandum\t11/03/2025\tCommendation for the month of October Best in Office Attendance\tTIBallesteros\tDone",
    "ICTU-INDOC-2025-0041.pdf\tICTU-INDOC-2025-0041\tMemorandum\t11/03/2025\tCommendation for the month of October Best in Office Attendance\tDRFlorendo\tDone",
    "ICTU-INDOC-2025-0042.pdf\tICTU-INDOC-2025-0042\tMemorandum\t11/03/2025\tCommendation for the month of October Best in Office Attendance\tMAAEbreo\tDone",
    "ICTU-INDOC-2025-0043.pdf\tICTU-INDOC-2025-0043\tMemorandum\t11/03/2025\tCommendation for the month of October Best in Office Attendance\tEDLiwanag\tDone",
    "ICTU-INDOC-2025-0044.pdf\tICTU-INDOC-2025-0044\tMemorandum\t11/03/2025\tCommendation for the month of October Best in Office Attendance\tMADeGuzman\tDone",
    "ICTU-INDOC-2025-0044.pdf\tICTU-INDOC-2025-0045\tMemorandum\t11/03/2025\tCommendation for the month of October Best in Office Attendance\tJPMarquez\tDone",
    "\tICTU-INDOC-2025-0046\tMemorandum\t12/04/2025\tCommendation for the month of October Best in Office Attendance\tEDLiwanag\tOngoing",
    "\tICTU-INDOC-2025-0047\tMemorandum\t12/04/2025\tCommendation for the month of October Best in Office Attendance\tMAOfiaza\tOngoing",
    "\tICTU-INDOC-2025-0048\tMemorandum\t12/04/2025\tCommendation for the month of October Best in Office Attendance\tJPMarquez\tOngoing",
    "PICTO-INDOC-2026-0001.pdf\tPICTO-INDOC-2026-0001\tMemorandum\t01/21/2026\tDesignation as Focal Person for Centralized Certificate Printing Services\tDLLibadia\tDone",
    "\tPICTO-INDOC-2026-0002\tMemorandum\t02/02/2026\tTardiness for the month of January 2026\tMGCasilla\tDone",
    "PICTO-INDOC-2026-0003.pdf\tPICTO-INDOC-2026-0003\tMemorandum\t02/05/2026\tCommendation for the month of January Best in Office Attendance\tMSCacdac\tDone",
    "PICTO-INDOC-2026-0004.pdf\tPICTO-INDOC-2026-0004\tMemorandum\t02/05/2026\tCommendation for the month of January Best in Office Attendance\tJGDucusin\tDone",
    "PICTO-INDOC-2026-0005.pdf\tPICTO-INDOC-2026-0005\tMemorandum\t02/05/2026\tCommendation for the month of January Best in Office Attendance\tEDLiwanag\tDone",
    "PICTO-INDOC-2026-0006.pdf\tPICTO-INDOC-2026-0006\tMemorandum\t02/05/2026\tCommendation for the month of January Best in Office Attendance\tJVNToribio\tDone",
];

function normalizeDate(?string $value): ?string
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    foreach (['n/j/Y', 'm/d/Y', 'n/j/y', 'm/d/y'] as $format) {
        $dt = DateTime::createFromFormat($format, $value);
        if ($dt && $dt->format($format) === $value) {
            return $dt->format('Y-m-d');
        }
    }

    $ts = strtotime($value);
    return $ts ? date('Y-m-d', $ts) : null;
}

function normalizeStatus(string $value): string
{
    return match (strtoupper(trim($value))) {
        'DONE', 'COMPLETED' => 'COMPLETED',
        'DELIVERED' => 'DELIVERED',
        'ONGOING', 'IN_PROGRESS' => 'ONGOING',
        default => 'ONGOING',
    };
}

function detectDocumentType(string $type): string
{
    $normalized = strtoupper(trim($type));
    return match ($normalized) {
        'SPECIAL ORDER' => 'SO',
        'MEMORANDUM', 'MEMO' => 'MEMO',
        default => 'OTHERS',
    };
}

$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=pictorts;charset=utf8mb4',
    'root',
    'root',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$pictoOfficeId = (int) $pdo->query("SELECT id FROM offices WHERE code = 'PICTO' LIMIT 1")->fetchColumn();

if (! $pictoOfficeId) {
    fwrite(STDERR, "PICTO office not found." . PHP_EOL);
    exit(1);
}

$checkStmt = $pdo->prepare('SELECT id FROM documents WHERE dts_number = ? OR doc_number = ? LIMIT 1');
$insertStmt = $pdo->prepare(
    'INSERT INTO documents (
        dts_number, transaction_number, picto_number, doc_number, memorandum_number, period, particulars,
        document_type, direction, originating_office, to_office, current_office, current_holder,
        subject, action_required, endorsed_to, date_received, status, remarks, shared_drive_link,
        received_via_online, encoded_by, created_at, updated_at
    ) VALUES (
        :dts_number, :transaction_number, :picto_number, :doc_number, :memorandum_number, :period, :particulars,
        :document_type, :direction, :originating_office, :to_office, :current_office, :current_holder,
        :subject, :action_required, :endorsed_to, :date_received, :status, :remarks, :shared_drive_link,
        :received_via_online, :encoded_by, :created_at, :updated_at
    )'
);

$inserted = 0;
$skipped = 0;

$pdo->beginTransaction();

try {
    foreach ($lines as $index => $rawLine) {
        $row = str_getcsv($rawLine, "\t", '"');
        $row = array_pad($row, 7, '');

        [$sharedDriveLink, $dtsNumber, $typeOfDocument, $dateValue, $subject, $particulars, $status] = $row;

        $dtsNumber = trim($dtsNumber);
        if ($dtsNumber === '') {
            continue;
        }

        $pictoDts = str_starts_with($dtsNumber, 'ICTU-')
            ? 'PICTO-' . substr($dtsNumber, 5)
            : $dtsNumber;

        $checkStmt->execute([$pictoDts, $pictoDts]);
        if ($checkStmt->fetchColumn()) {
            $skipped++;
            echo "Skipped existing row: {$pictoDts}" . PHP_EOL;
            continue;
        }

        $normalizedDate = normalizeDate($dateValue);
        $normalizedStatus = normalizeStatus($status);
        $documentType = detectDocumentType($typeOfDocument);

        $insertStmt->execute([
            'dts_number' => $pictoDts,
            'transaction_number' => $pictoDts,
            'picto_number' => $pictoDts,
            'doc_number' => $pictoDts,
            'memorandum_number' => null,
            'period' => null,
            'particulars' => trim($particulars) !== '' ? trim($particulars) : null,
            'document_type' => $documentType,
            'direction' => 'INCOMING',
            'originating_office' => $pictoOfficeId,
            'to_office' => null,
            'current_office' => $pictoOfficeId,
            'current_holder' => null,
            'subject' => trim($subject) !== '' ? trim($subject) : '[No Subject]',
            'action_required' => null,
            'endorsed_to' => null,
            'date_received' => $normalizedDate,
            'status' => $normalizedStatus,
            'remarks' => null,
            'shared_drive_link' => trim($sharedDriveLink) !== '' ? trim($sharedDriveLink) : null,
            'received_via_online' => 0,
            'encoded_by' => null,
            'created_at' => $normalizedDate ? ($normalizedDate . ' 00:00:00') : null,
            'updated_at' => $normalizedDate ? ($normalizedDate . ' 00:00:00') : null,
        ]);

        $inserted++;
        echo "Inserted: {$pictoDts}" . PHP_EOL;
    }

    $pdo->commit();
    echo PHP_EOL . "Import complete." . PHP_EOL;
    echo "Inserted: {$inserted}" . PHP_EOL;
    echo "Skipped: {$skipped}" . PHP_EOL;
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Import failed at row ' . ($index + 1) . ': ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
