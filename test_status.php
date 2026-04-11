<?php
// Test status update endpoint
$data = ['status' => 'done'];
$ch = curl_init('http://localhost:8000/todos/1/update-status');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-Requested-With: XMLHttpRequest']);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo 'Status: ' . $httpCode;
echo 'Response: ' . $response;
curl_close($ch);
