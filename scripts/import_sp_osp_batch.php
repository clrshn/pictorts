<?php

declare(strict_types=1);

$lines = [
    "\tICTU-SP-2024-0001\t1/17/2024\tSP-BM Annabelle\tPermission to Invite Concerned DH to the Committee Hearing\tOrdinance 409-2022 LUICTC; Referral that emanated to 35th Regular Session dated Feb. 28, 2023 - Wifi Connectivity of Learners\tAttendance to Committee Hearing on January 18, 2024; 9:30AM at SP Bayview Hall\tGDB, MECE, JRM\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0002\t1/18/2024\tOPA\tIst Indorsement- endorsing to the Office of the PHO and ICTU the attached letter from the SP\tInvitation for Dr Dan Dacanay and Gerry Binas-o to the joint Committee Hearing to discuss draft Provincial Ordinance No. 409-2022\tAttendance to Committee Hearing on January 18, 2024; 9:30AM at SP Bayview Hall\tGDB\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0003\t1/19/2023\tSP\t1 day technical review/assessment and action planning with the SP LTMU staff on January 22, 2024, 10am-5pm\t\tLIS- January 22, 2024, 10am to 5pm\t\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0004\t1/22/23\tOPA\t1 day technical review assessment\t\tFor appropriate action\tSha, prepare our output on this. \tDone\twith referral/action plan\tFALSE\t\t",
    "\tICTU-SP-2024-0005\t1/22/2024\tSP\tSend-off program for ex-officio member Hon. Geraldine R. Ortega\tJanuary 23, 2024 , Speaker pro-empore francisco ortega provincial legislative building and session hall\t\tgdb\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0006\t1/29/2024\tSP\tPermission to invite the concerned Department Heads to the Committee Hearing\tJoint Committee Hearing\tattend the Face to face Joint Committee Hearing on Feb. 2, 2024, 9am at SP Bayview Hall\tGDB\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0007\t2/14/2024\tSP\tforwarding a copy of SP Resolution No. 79-2024 for Information and Guidance\tthe request of DITO Telecommunity for a Submarine Cable LAnding Station in Baccuit SUr, Bauang, La Union\t\t\tNone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0008\t5/20/2024\tSP\tInvitation for Attendance to the Committee Hearing\tKALSADA PROGRAM\tattend Committee Meeting at SP Bayview, May 23, 2024; 9:30AM\tGDB\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0009\t6/6/2024\tSP\t2023-2025 Legislative Agenda Focus Group Discussion 2024\tJune 14, 2024, Friday, at the SP Bayview Hall\tAttendance to the Legislative Agenda Status Update and Evaluaion Review\tGDB\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0010\t6/21/2024\tSP\tInvitation for Attendance to the Committee Hearing\tKALSADA PROGRAM; official website, findjobs\tAttendance June 26, 2024, Vista la Vita, 9:30AM\tGDB\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0011\t6/26/2024\tSP\tInvitation for Attendance to the 105th Regular Session\t\tDHs, OPG Work UHs and COHs on July 2, 2024, 3:00PM at the Speaker Pro-Tempore Legislative Building\tGDB\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0012\t6/26/2024\tSP\tRequest for Assistance for the conduct of the SOPA 2024\tJuly 2, 2024, 3PM, 105th Regulae Session, Legislative Building\tTechnical requirements and connectivity\tGDB\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0013\t7/11/2024\tSP\tInvitation to Committee Hearing\t60-day Free Trial Service of the Paper-less Document Tracking System (P-DTS)\tattendance\tGDB\tDone\t\tFALSE\t\t",
    "\tICTU-SP-2024-0014\t7/23/2024\tSP\tInvitation for Attendance of Committee Hearing\tCY 2025 AIP of ICTU\tattendance and compliance\\nJuly 25, 2024\\n9:00AM, SP Bayview Hall\tGDB\tOngoing\t\tFALSE\t\t",
    "\tICTU-SP-2024-0015\t8/5/2024\tSP\tInvitation for Attendance of Committee Hearing\tCY 2025 AIP of ICTU\tattendance and compliance\\nAugust 8, 2024\\n10:00AM, SP Bayview Hall\tGDB\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0016\t9/6/2024\tSP\tCompliance to Memo No. 335-2024\tEnd - User Satisfaction and Compliance Rating of PGLU Workspace\t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0017\t9/10/2024\tSP\tInvitation for the Attendance to the Committee Hearing\tSeptember 12, 2024 9:30AM @ SP Bayview Hall of the Speaker Pro-Tempore Francisco I. Ortega Legislative Bldg \t\tJamie\t\tJoin me on this\tFALSE\t\t",
    "\tICTU-SP-2024-0018\t9/10/2024\tSP\tLIS \tfor compliance \t\tMECE\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0019\t9/23/2024\tSP\tInvitation for the Attendance to the Committee Hearing\tSeptember 26, 2024 9:30AM @ SP Bayview Hall of the Speaker Pro-Tempore Francisco I. Ortega Legislative Bldg \t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0020\t9/26/24\tSP\tForwarding a copy of SP Resolution No. 1457-2024 for Information and Guidance\tBalay Namnama/bahay Pag-asa dated June 26, 2024\t\t\t\tTJ/Allan, kindly draft our response letter to this\tFALSE\t\t",
    "\tICTU-SP-2024-0021\t9/26/24\tSP\tForwarding a copy of SP Resolution No. 1460-2024 for Information and Guidance\tto check Internet Connection of the facility Balay Namnama/Bahay Pag-asa Sta. rita Bacnota La Union\t\t\t\tTJ/Allan, kindly draft our response based on our office action relative to resolution \tFALSE\t\t",
    "\tICTU-SP-2024-0022\t10/01/2024\tSP\tLIS System Reports\tRequest for Certification of Completion of the LIS\t\t\t\tMs Len / Ash Please handle \tFALSE\t\t",
    "\tICTU-SP-2024-0023\t10/04/2024\tSP\tLegislative Information System \tThank you letter for reviewing letter and recommendation on enhancing the security features of the LIS\t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0024\t10/15/2024\tSP\tLegislative Information System \tOctober 21, 2024 Permission to allow ICTU Team for the presentation of the result of the assessment of LIS\t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0025\t10/22/2024\tSP\tRequest of SP for the OPG-ICTU to present the result of the assessment of the Legislative Information System \tOctober 21, 2024\t\t\tfor filing\t\tFALSE\t\t",
    "\tICTU-SP-2024-0026\t11/7/24\tSP\tForwarding a copy of SP Resolution No. 1640-2024 for Information and Guidance\tAttached Resolution No 1640-2024\t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0027\t11/7/24\tSP\tForwarding a copy of SP Resolution No. 1639-2024 for Information and Guidance\tAttached Resolution No 1639-2024\t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0028\t11/13/2024\tSP\tInvitation for Attendance to the Committee Hearing Kalsada Group \tNovember 21, 2024 9:30am at SP View Multi Purpose  of the Speaker Pro-Tempore Francisco I. Ortega Legislative Building, Provincial Capitol San Fernando La Union \t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0029\t11/14/2024\tSP\tSP Resolution No. 1639-2024\tApproved the amended CY 2025 AIP  of the Office of the Provincial Governor-ICTU\t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0030\t11/14/2024\tSP\tBudget Hearing\tAll members of the Local Finance Committee and concerned Department/UHs of each offices/Units to present/defend their proposed FY 2025 budgets. Nov. 18 and 20, 2024 at Bayview Hall, SP Building\t\t\t\t\tFALSE\t\t",
    "\tICTU-SP-2024-0031\t11/27/2024\tSP\tPost Evaluation of the Committee on Public Works and Utilities and the Kalsada Group/ Program\tSir GDB to attend the Post Evaluation on December 6-7 at Soto Grande Hotel Baguio City\t\t\tOngoing\t\tFALSE\t\t",
    "\tICTU-SP-2024-0032\t12/2/2024\tSP\tPost-Evaluation of the Committee on Public Works and Utilities and the Kalsada Group/Program\tInvitation to attend the Post-Evaluation of the Committee on December 6-7, 2024 at Soto Grande Hotel, Baguio City\t\t\t\t\tFALSE\t\tOPG-OTH-11-24-227",
    "\tICTU-SP-2024-0033\t12/4/2024\tSP\tRequest for Installation of additional Biometric Machine \tBiometric Machine on the 3rd floor basement of the Speaker Pro-tempore Francisco Ortega\t\t\tOngoing\tfor filing\tFALSE\t\t",
    "ICTU-SP-2025-0001 Installation of biometrics terminal at the SP Secretariat Office, 3rd basement of SP Building.PDF\tICTU-SP-2025-0001\t3/6/2025\tSP\tRequest assistance\tInstallation of biometrics terminal at the SP Secretariat Office, 3rd basement of SP Building\t\tDone\tGlaiza, Please handle\tFALSE\t\t",
    "\tICTU-SP-2025-0002\t3/6/2025\tSP\tDraft Provincial Ordinance No. 536-2025\tAn Ordinance Creating the Provincial Information and Communicationa Technology Office (PICTO), and providing for its Tasks, Functions, Personnel, and Appropriation\t\tDone\t\tFALSE\t\t",
    "ICTU-SP-2025-0003 List of executive offices with no update on the LA as of March 5, 2025.PDF\tICTU-SP-2025-0003\t3/31/2025\tSP\tRequest \tList of executive offices with no update on the LA as of March 5, 2025\tRequest immediate submission of the concerned offices  their respective status Report on their identified requested legislative measures under the Legislative Agenda (LA)\t\tDone\t\tFALSE\t\t",
    "ICTU-SP-2025-0004 Request for Review and Recommendations of the Second Draft of the Manual of the LIS.PDF\tICTU-SP-2025-0004\t5/8/2025\tSP\tRequest for Review and Recommendations of the Second Draft of the Manual of the LIS\t2nd draft of the manual of the Legislative Information System(LIS)\t\t\tDone\t\tFALSE\tDMS, Kindly make a thorough review of this document and check if our comment were incorporated as stated. You may request an access for validation \t",
    "Copy ICTU-SP-2025-0005 Invitation for Attendance to the Committee Hearing.PDF\tICTU-SP-2025-0005\t5/26/2025\tSP\tInvitation for Attendance to the Committee Hearing\tMay 29, 2025 9:30AM at the SP Bay View MultiPurpose\t\t\tDone\t\tFALSE\t\t",
    "ICTU-SP-2025-0006 Invitation for Attendance to the Committee Hearing.PDF\tICTU-SP-2025-0006\t5/29/2025\tSP\tInvitation for Attendance to the Committee Hearing\tdiscuss the Draft Provincial Ordinance No. 536-2025 entitiled: AN ordinance creating the PICTOJune 4, 2025, 1:00pm at Speaker Pro-Tempore Provincial Legislative Building\t\t\tDone\t\tFALSE\t\t",
    "ICTU-SP-2025-0007 Invitation for Attendance to the Committee Hearing.PDF\tICTU-SP-2025-0007\t6/9/2025\tSP\tInvitation for Attendance to the Committee Hearing\tJune 13, 2025 9:30am at SP Bay View\t\t\t\tMs Len represent me on this.\tFALSE\t\t",
    "ICTU-SP-2025-0008 SP Resolution No 1171-2025 .PDF\tICTU-SP-2025-0008\t07/07/2025\tSP\tForwarding a copy of SP Resolution No. 1171-2025 for information and Guidance \t\t\t\tFor Filing\tFALSE\t\t",
    "ICTU-LET-2025-0144 SP Resolution No. 1171-2025.PDF\tICTU-SP-2025-0009\t07/18/2025\tSP\tForwarding a copy of SP Resolution No. 1327-2025 for information and Guidance \tOrganizing the Standard Committees of the 24th Sanguniang Panlalawigan of La Union for the term 2025-2028\t\t\t\t\tFALSE\t\t",
    "ICTU-SP-2025-0010 Forwarding a copy of SP Resolution No. 1429-2025 for information and Guidance.pdf\tICTU-SP-2025-0010\t08/14/2025\tSP\tForwarding a copy of SP Resolution No. 1429-2025 for information and Guidance \tRequesting Honorable Governor Mario Eduardo C. Ortega for the Reactivation of the Kalsada Group and the Kalsada Road Clearing Team to facilitate efforts of various agencies in clearing roads and restore accesibility in areas affected by typhoon Emong\t\t\tOngoing\t\tFALSE\t\t",
    "\tICTU-SP-2025-0011\t09/11/2025\tSP\tTo attend cum commitee hearing \tSept 19, 2025 10am at Speaker Pro-Tempore Francisco I. Ortega Provincial Legislative Building at Session Hall Provincial Capitol San Fernando City La Union\t\t\tOngoing\t\tFALSE\t\t",
    "ICTU-SP-2025-0012 Notice of Re-scheduling of onboarding cum committee hearing of the Committee on Information and Communication Technology .pdf\tICTU-SP-2025-0012\t09/17/2025\tSP\tNotice of Re-scheduling of onboarding cum committee hearing of the Committee on Information and Communication Technology \tOctober 9, 2025 10am at Speaker Pro-Tempore Francisco I. Ortega Provincial Legislative Building at Session Hall Provincial Capitol San Fernando City La Union\t\t\tOngoing\t\tFALSE\t\t",
    "ICTU-SP-2025-0013 Request to condemn equipment to the undersigned.pdf\tICTU-SP-2025-0013\t09/25/2025\tSP\tRequest to condemn equipment to the undersigned \t\t\t\tOngoing\t\tFALSE\t\t",
    "ICTU-SP-2025-0014 Request for reset of login credentials of the Phil Councilors League - La Union Chapter.pdf\tICTU-SP-2025-0014\t10/9/2025\tSP\tRequest for reset of login credentials of the Phil Councilors League - La Union Chapter\tcoordinate with Mr. Oliver Niño Gundran 09171352421\t\t\tOngoing\tJam please handle\tFALSE\t\t",
    "ICTU-SP-2025-0015 Webinar aims to educate COS,Casual and JO of the PGLU on their rights RA11313.pdf\tICTU-SP-2025-0015\t10/20/2025\tSP\tWebinar aims to educate COS,Casual and JO of the PGLU on their rights RA11313\thttp://bit.ly/3KRcqup - for registration, http://bit.ly/4nxGOaR - pre assessment, http://bit.ly/4hlKLO7 post assessment  October 24, 2025 9-11am\t\t\tOngoing\t\tFALSE\t\t",
    "ICTU-SP-2025-0016 To attend commitee hearing on laws rules and privileges and justice and human rights.pdf\tICTU-SP-2025-0016\t10/21/2025\tSP\tTo attend commitee hearing on laws rules and privileges and justice and human rights\tOctober 22, 2025 1PM at Bayview \t\t\tOngoing\tfor filing\tFALSE\t\t",
    "ICTU-SP-2025-0017 Request for email account change.pdf\tICTU-SP-2025-0017\t10/24/2025\tSP\tRequest for email account change \t\t\t\tOngoing\t\tFALSE\t\t",
    "ICTU-SP-2025-0018 Forwarding a copy of SP Resolution No. 1741 -2025 for information and guidance .pdf\tICTU-SP-2025-0018\t11/03/2025\tSP\tForwarding a copy of SP Resolution No. 1741 -2025 for information and guidance \tReconstituting and expanding the compositionof the Provincial Disaster Risk Reduction and Management Council of the Province of la Union pursuant to Rep. Act No. 10121 Otherwise known as the Phili. Disaster Risk  Reduction and Management Act of 2010\t\t\tOngoing\tAdmin, for filing for future references\tFALSE\t\t",
    "ICTU-SP-2025-0019.pdf\tICTU-SP-2025-0019\t11/12/2025\tSP\tInvite COS,Casual and JO of the PGLU \"with great power comes great responsibility:upholding accountability in government service\" \thttp://bit.ly/49fXVKC - for registration, http://bit.ly/3WNlt11 - pre assessment, http://bit.ly/3WR0JXa post assessment  November 14, 2025 9-11am\t\t\tOngoing\tTo all concerned attend as spitulated\tFALSE\t\t",
    "ICTU-SP-2025-0020 Budget Hearing Novemebr 25 and Nov 26, 2025 at Speaker Pro-Tempore Francisco I. Ortega bayview Hall, Sangguniang Panlalawigan Building Provincial Capitol .pdf\tICTU-SP-2025-0020\t11/20/2025\tSP\tBudget Hearing Novemebr 25 and Nov 26, 2025 at Speaker Pro-Tempore Francisco I. Ortega bayview Hall, Sangguniang Panlalawigan Building Provincial Capitol \t1:00-1:30pm \t\t\tOngoing\t\tFALSE\t\t",
    "ICTU-SP-2025-0021 Forwarding a copy of SP Resolution No. 2827-2025 for Information and Appropriate Action (2).pdf\tICTU-SP-2025-0021\t12/03\tSP\tForwarding a copy of SP Resolution No. 2827-2025 for Information and Appropriate Action\t\t\t\tOngoing\tAdmin, for filing and future references\tFALSE\t\t",
    "PICTO-OSP-2026-0001.PDF\tPICTO-OSP-2026-0001\t01/16/2026\tSP\tForwarding a copy of SP Resolution No. 2906 -2025 for information and guidance\t\t\t\tOngoing\tfor filing\tFALSE\t\t",
    "PICTO-OSP-2026-0002.pdf\tPICTO-OSP-2026-0002\t02/02/2026\tSP\tCommittee Hearing of the Committee on Laws , Rules and Privileges and JUstice and Human Rights, PA, PLO, PEO, PICTO\tFebruary 5, 2026, 1:00PM at the Bayview Hall of the Speaker Pro Tempore Francisco I. Ortega Building and Session Hall\tfor reference you may access the copy at https://bit.ly/Feb5MeetingAttachments\t\tOngoing\tfor re schedule\tFALSE\tOPA, PLO, PEO, PICTO please attend\tADM 02-2026-00278",
    "PICTO-OSP-2026-0003.pdf\tPICTO-OSP-2026-0003\t02/03/2026\tSP\tRequest letter\tRequest to expand the allocated storage for the Legislative Information System (LIS)\t\t\tOngoing\tTJ, Pls handle \tFALSE\t\t",
    "PICTO-OSP-2026-0004.pdf\tPICTO-OSP-2026-0004\t02/04/2026\tSP\tForwarding a Copy of SP Resolution No. 1976-2025 \tfor your information , reference and guidance\t\t\tOngoing\t\tFALSE\t\t",
    "\tPICTO-OSP-2026-0005\t2/12/2026\tSP\tCommittee on ICT\tAgenda: Status of Wireless Mesh\tFebruary 20, 2026 | SP Annex\t\tOngoing\t\tFALSE\t\t",
    "\tICTU-LUP-MEMO-2025-001\t03/13/2025\tLUPGEO\tLUPGEO Memo No. 02\tLUPGEO Officers and Members\tLUPGEO General Assembly\tMarch 19, 2025 1:00PM via Zoom\\n\\n\\nCNA and DBM MC - bit.ly/GenAssembly2025_CNARatification\tMembers are required to attend, non members are encouraged to join as well\t\tDone\tTo all members attend as requested\t",
];

$officeAliases = [
    'SP-BM Annabelle' => 'SP',
];

function normalizeDateFromDts(string $dtsNumber, string $value): ?string
{
    $value = trim($value);
    if ($value === '') {
        return null;
    }

    if ($value === '12/03') {
        return '2025-12-03';
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

function normalizeStatusAndRemarks(string $rawStatus, ?string $remarks): array
{
    $rawStatus = trim($rawStatus);
    $remarks = trim((string) $remarks);
    $normalized = strtoupper($rawStatus);

    if (in_array($normalized, ['DONE', 'COMPLETED'], true)) {
        return ['COMPLETED', $remarks !== '' ? $remarks : null];
    }

    if ($normalized === 'DELIVERED') {
        return ['DELIVERED', $remarks !== '' ? $remarks : null];
    }

    if (in_array($normalized, ['ONGOING', 'IN_PROGRESS'], true)) {
        return ['ONGOING', $remarks !== '' ? $remarks : null];
    }

    if ($normalized === '') {
        return ['ONGOING', $remarks !== '' ? $remarks : null];
    }

    $mergedRemarks = $remarks === '' ? $rawStatus : ($remarks . ' | Original Status: ' . $rawStatus);
    return ['ONGOING', $mergedRemarks];
}

function normalizeBool(?string $value): int
{
    return in_array(strtoupper(trim((string) $value)), ['1', 'TRUE', 'YES'], true) ? 1 : 0;
}

function detectDocumentType(string $dtsNumber): string
{
    if (str_contains($dtsNumber, '-MEMO-')) {
        return 'MEMO';
    }

    return 'SP';
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

$officeMap = [];
foreach ($pdo->query('SELECT id, code FROM offices') as $office) {
    $officeMap[$office['code']] = (int) $office['id'];
}

$checkStmt = $pdo->prepare('SELECT id FROM documents WHERE dts_number = ? OR doc_number = ? LIMIT 1');
$insertStmt = $pdo->prepare(
    'INSERT INTO documents (
        dts_number, transaction_number, picto_number, doc_number, memorandum_number, period, particulars,
        document_type, direction, originating_office, to_office, current_office, current_holder,
        subject, action_required, endorsed_to, date_received, status, remarks, shared_drive_link,
        received_via_online, encoded_by, administrators_instruction, opa_reference_no, created_at, updated_at
    ) VALUES (
        :dts_number, :transaction_number, :picto_number, :doc_number, :memorandum_number, :period, :particulars,
        :document_type, :direction, :originating_office, :to_office, :current_office, :current_holder,
        :subject, :action_required, :endorsed_to, :date_received, :status, :remarks, :shared_drive_link,
        :received_via_online, :encoded_by, :administrators_instruction, :opa_reference_no, :created_at, :updated_at
    )'
);

$inserted = 0;
$skipped = 0;

$pdo->beginTransaction();

try {
    foreach ($lines as $index => $rawLine) {
        $line = str_replace('\\n', "\n", $rawLine);
        $row = str_getcsv($line, "\t", '"');
        $row = array_pad($row, 13, '');

        [$sharedDriveLink, $dtsNumber, $dateReceived, $officeCode, $subject, $particulars, $actionRequired, $endorsedTo, $status, $remarks, $receivedViaEmail, $opaActionSlip, $opaReferenceNumber] = $row;

        $dtsNumber = trim($dtsNumber);
        if ($dtsNumber === '') {
            continue;
        }

        $officeCode = trim($officeCode);
        $officeCode = $officeAliases[$officeCode] ?? $officeCode;

        if (! isset($officeMap[$officeCode])) {
            throw new RuntimeException("Unknown office code '{$officeCode}' at row " . ($index + 1));
        }

        $normalizedDate = normalizeDateFromDts($dtsNumber, $dateReceived);
        if ($normalizedDate === null) {
            throw new RuntimeException("Invalid date '{$dateReceived}' for {$dtsNumber}");
        }

        $pictoDts = str_starts_with($dtsNumber, 'ICTU-')
            ? 'PICTO-' . substr($dtsNumber, 5)
            : $dtsNumber;

        [$normalizedStatus, $normalizedRemarks] = normalizeStatusAndRemarks($status, $remarks);

        $checkStmt->execute([$pictoDts, $pictoDts]);
        if ($checkStmt->fetchColumn()) {
            $skipped++;
            echo "Skipped existing row: {$pictoDts}" . PHP_EOL;
            continue;
        }

        $documentType = detectDocumentType($pictoDts);

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
            'originating_office' => $officeMap[$officeCode],
            'to_office' => null,
            'current_office' => $officeMap['PICTO'] ?? null,
            'current_holder' => null,
            'subject' => trim($subject) !== '' ? trim($subject) : '[No Subject]',
            'action_required' => trim($actionRequired) !== '' ? trim($actionRequired) : null,
            'endorsed_to' => trim($endorsedTo) !== '' ? trim($endorsedTo) : null,
            'date_received' => $normalizedDate,
            'status' => $normalizedStatus,
            'remarks' => $normalizedRemarks,
            'shared_drive_link' => trim($sharedDriveLink) !== '' ? trim($sharedDriveLink) : null,
            'received_via_online' => normalizeBool($receivedViaEmail),
            'encoded_by' => null,
            'administrators_instruction' => trim($opaActionSlip) !== '' ? trim($opaActionSlip) : null,
            'opa_reference_no' => trim($opaReferenceNumber) !== '' ? trim($opaReferenceNumber) : null,
            'created_at' => $normalizedDate . ' 00:00:00',
            'updated_at' => $normalizedDate . ' 00:00:00',
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
    fwrite(STDERR, 'Import failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
