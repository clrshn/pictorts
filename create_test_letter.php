<?php

// Test Letter document creation
require_once 'vendor/autoload.php';

use App\Models\Document;
use App\Models\Office;
use App\Models\User;

// Create a test Letter document
$document = Document::create([
    'dts_number' => 'LET-2024-0001',
    'picto_number' => 'LET-ICTU-2024-001',
    'document_type' => 'LETTER',
    'direction' => 'OUTGOING',
    'originating_office' => 1, // Assuming ICTU office has ID 1
    'to_office' => 2, // Assuming destination office
    'current_office' => 1,
    'current_holder' => 1, // Assuming user ID 1
    'subject' => 'Test Letter Document for System Validation',
    'action_required' => 'Please verify all Letter fields are working correctly',
    'endorsed_to' => 'System Administrator',
    'date_received' => now(),
    'status' => 'ONGOING',
    'remarks' => 'Test document created to validate Letter field functionality',
    'opg_reference_no' => 'OPG-2024-001',
    'opa_reference_no' => 'OPA-2024-001',
    'governors_instruction' => 'Test instruction for Governor validation',
    'administrators_instruction' => 'Test instruction for Administrator validation',
    'returned' => 'Not returned',
    'opg_action_slip' => 'OPG-ACTION-001',
    'dts_no' => 'DTS-2024-001',
    'encoded_by' => 1,
]);

echo "Letter document created with ID: " . $document->id;
echo "All Letter fields populated:";
echo "- OPG Reference No: " . $document->opg_reference_no;
echo "- OPA Reference No: " . $document->opa_reference_no;
echo "- Governor's Instruction: " . $document->governors_instruction;
echo "- Administrator's Instruction: " . $document->administrators_instruction;
echo "- Returned: " . $document->returned;
echo "- OPG Action Slip: " . $document->opg_action_slip;
echo "- DTS No: " . $document->dts_no;
