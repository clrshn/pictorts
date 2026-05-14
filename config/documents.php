<?php

return [
    /**
     * Document Types
     * 
     * Add new document types here. They will automatically:
     * - Appear in all dropdown forms
     * - Be counted in the document type counts section
     * - Be available for filtering
     * - Be validated in store/update requests
     */
    'types' => [
        'MEMO',
        'EO',
        'SO',
        'LETTER',
        'SP',
        'TO',
        'OTHERS',
    ],

    /**
     * Travel Order Types
     * 
     * Add new travel order types here. They will automatically:
     * - Appear in travel order dropdowns
     * - Be available for filtering
     * - Be validated in store/update requests
     */
    'travel_order_types' => [
        'WITHIN_LA_UNION',
        'OUTSIDE_LA_UNION',
        'SPECIAL_ORDER',
    ],
];
