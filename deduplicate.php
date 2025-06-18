<?php

function loadLeads($filename) {
    $json = file_get_contents($filename);
    return json_decode($json, true)['leads'];
}

function saveLeads($leads, $filename) {
    file_put_contents($filename, json_encode(['leads' => array_values($leads)], JSON_PRETTY_PRINT));
}

function saveLog($log, $filename) {
    file_put_contents($filename, json_encode($log, JSON_PRETTY_PRINT));
}

function compareDates($a, $b) {
    $dateA = new DateTime($a['entryDate']);
    $dateB = new DateTime($b['entryDate']);

    return $dateA <=> $dateB;
}

function generateChangeLog($old, $new) {
    $changes = [];
    foreach ($old as $key => $value) {
        if ($key === 'entryDate') continue;
        if (isset($new[$key]) && $new[$key] !== $value) {
            $changes[] = [
                'field' => $key,
                'from' => $value,
                'to' => $new[$key]
            ];
        }
    }
    return $changes;
}

$inputFile = 'leads.json';
$outputFile = 'output.json';
$logFile = 'changelog.json';

$originalLeads = loadLeads($inputFile);
$deduped = [];
$log = [];

foreach ($originalLeads as $index => $lead) {
    $id = $lead['_id'];
    $email = $lead['email'];

    $duplicateKey = null;
    foreach ($deduped as $key => $existing) {
        if ($existing['_id'] === $id || $existing['email'] === $email) {
            $duplicateKey = $key;
            break;
        }
    }

    if ($duplicateKey !== null) {
        $existing = $deduped[$duplicateKey];
        $replace = false;

        $dateCompare = compareDates($existing, $lead);

        if ($dateCompare < 0) {
            $replace = true;
        } elseif ($dateCompare === 0) {
            $replace = true; // prefer later in list if same date
        }

        if ($replace) {
            $deduped[$duplicateKey] = $lead;
            $log[] = [
                'source' => $existing,
                'replacement' => $lead,
                'changes' => generateChangeLog($existing, $lead)
            ];
        } else {
            $log[] = [
                'source' => $lead,
                'replacement' => $existing,
                'changes' => generateChangeLog($lead, $existing)
            ];
        }
    } else {
        $deduped[] = $lead;
    }
}

saveLeads($deduped, $outputFile);
saveLog($log, $logFile);

echo "De-duplication complete.\n";
echo "Output saved to $outputFile\n";
echo "Change log saved to $logFile\n";
