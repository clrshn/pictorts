<?php

declare(strict_types=1);

$rows = [
    [
        'target_doc_number' => 'PICTO-PLO-EO-2024-000001',
        'dts_number' => 'PICTO-LET-2024-0084',
        'date_received' => '2024-06-25',
        'originating_office_code' => 'PLO',
        'memorandum_number' => 'Executive Order No. 33 Series 2024',
        'subject' => "An Executive Order Strengthening and Enhancing the Procedures of the PGLU 8888 Citizens' Complaint and Action Center",
        'particulars' => "Provincial Permanent Action Team\nGDB- Technical Officer",
        'period' => null,
        'action_required' => 'Recurring',
        'endorsed_to' => null,
        'status' => 'ONGOING',
        'remarks' => null,
        'received_via_online' => 0,
        'shared_drive_link' => null,
    ],
    [
        'target_doc_number' => 'PICTO-OPG-EO-2024-000001',
        'dts_number' => 'PICTO-EO-2024-002',
        'date_received' => '2024-07-02',
        'originating_office_code' => 'OPG',
        'memorandum_number' => 'Executive Order No. 35 S. 2024',
        'subject' => 'Creating the Technical Working Group (TWG) for the Mst Business-Friendly Local Government Unit Awards of the Provincial Government of La Union',
        'particulars' => 'Most Business-Friendly LGU Award',
        'period' => null,
        'action_required' => 'Recurring',
        'endorsed_to' => null,
        'status' => 'ONGOING',
        'remarks' => null,
        'received_via_online' => 0,
        'shared_drive_link' => null,
    ],
    [
        'target_doc_number' => 'PICTO-OPG-EO-2025-000001',
        'dts_number' => 'PICTO-EO-2025-0001',
        'date_received' => '2025-07-14',
        'originating_office_code' => 'OPG',
        'memorandum_number' => 'Executive Order No. 1, Series of 2025',
        'subject' => 'Creating Bids and Awards Committee (BAC) of PGLU in accordance with RA 12009 and its IRR',
        'particulars' => "Chairman: Ramon B. Torres, PhD, CESE, PA\n",
        'period' => null,
        'action_required' => 'Recurring',
        'endorsed_to' => null,
        'status' => 'ONGOING',
        'remarks' => null,
        'received_via_online' => 0,
        'shared_drive_link' => null,
    ],
    [
        'target_doc_number' => 'PICTO-OPG-EO-2026-000001',
        'dts_number' => 'PICTO-EO-2026-0001',
        'date_received' => '2026-01-06',
        'originating_office_code' => 'OPG',
        'memorandum_number' => 'Executive Order No.1, series of 2026',
        'subject' => 'Creating and Organizing the 176th La Union Founding Anniversary Steering Committee',
        'particulars' => 'Chairperson: Hon. Mario Eduardo C. Ortega, Provincial Governor',
        'period' => null,
        'action_required' => 'Recurring',
        'endorsed_to' => null,
        'status' => 'ONGOING',
        'remarks' => null,
        'received_via_online' => 0,
        'shared_drive_link' => null,
    ],
    [
        'target_doc_number' => 'PICTO-OPG-EO-2026-000002',
        'dts_number' => 'PICTO-EO-2026-0002',
        'date_received' => '2026-02-11',
        'originating_office_code' => 'OPG',
        'memorandum_number' => 'Executive Order No. 41 S. 2026',
        'subject' => 'Reorganization of the La Union Innovation Council (LUIC)',
        'particulars' => 'Provincial Innovation Council was subsequently renamed as the La Union Innovation Council (LUIV)',
        'period' => null,
        'action_required' => 'Recurring',
        'endorsed_to' => null,
        'status' => 'ONGOING',
        'remarks' => null,
        'received_via_online' => 0,
        'shared_drive_link' => 'PICTO-EO-2026-0002.pdf',
    ],
];

$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=pictorts;charset=utf8mb4',
    'root',
    'root',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$officeMap = [];
foreach ($pdo->query('SELECT id, code FROM offices') as $office) {
    $officeMap[$office['code']] = (int) $office['id'];
}

$conflictStmt = $pdo->prepare('SELECT id, doc_number FROM documents WHERE dts_number = ? AND doc_number <> ? LIMIT 1');
$findStmt = $pdo->prepare('SELECT id FROM documents WHERE doc_number = ? LIMIT 1');
$updateStmt = $pdo->prepare(
    'UPDATE documents
     SET dts_number = :dts_number,
         date_received = :date_received,
         originating_office = :originating_office,
         current_office = :current_office,
         memorandum_number = :memorandum_number,
         subject = :subject,
         particulars = :particulars,
         period = :period,
         action_required = :action_required,
         endorsed_to = :endorsed_to,
         status = :status,
         remarks = :remarks,
         received_via_online = :received_via_online,
         shared_drive_link = :shared_drive_link,
         updated_at = NOW()
     WHERE id = :id'
);

$updated = 0;

$pdo->beginTransaction();

try {
    foreach ($rows as $row) {
        $findStmt->execute([$row['target_doc_number']]);
        $id = $findStmt->fetchColumn();

        if (! $id) {
            throw new RuntimeException("Missing existing EO row for doc_number {$row['target_doc_number']}");
        }

        $conflictStmt->execute([$row['dts_number'], $row['target_doc_number']]);
        $conflict = $conflictStmt->fetch();
        if ($conflict) {
            throw new RuntimeException("dts_number {$row['dts_number']} is already used by document {$conflict['doc_number']}");
        }

        $updateStmt->execute([
            'id' => $id,
            'dts_number' => $row['dts_number'],
            'date_received' => $row['date_received'],
            'originating_office' => $officeMap[$row['originating_office_code']],
            'current_office' => $officeMap['PICTO'] ?? null,
            'memorandum_number' => $row['memorandum_number'],
            'subject' => $row['subject'],
            'particulars' => $row['particulars'],
            'period' => $row['period'],
            'action_required' => $row['action_required'],
            'endorsed_to' => $row['endorsed_to'],
            'status' => $row['status'],
            'remarks' => $row['remarks'],
            'received_via_online' => $row['received_via_online'],
            'shared_drive_link' => $row['shared_drive_link'],
        ]);

        $updated++;
        echo "Updated: {$row['target_doc_number']} -> {$row['dts_number']}" . PHP_EOL;
    }

    $pdo->commit();
    echo PHP_EOL . "Sync complete." . PHP_EOL;
    echo "Updated: {$updated}" . PHP_EOL;
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Sync failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
