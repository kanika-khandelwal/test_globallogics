# PHP Deduplication Challenge

This repository contains the solution to the PHP deduplication challenge.

## Problem Statement

Take a variable number of identically structured JSON records and de-duplicate the set based on:
- Unique IDs (`_id`)
- Unique Emails (`email`)
- Prefer the latest `entryDate`
- If `entryDate` is same, prefer later record in input

A change log is also generated showing:
- Original vs Replacement records
- Field-wise differences

## Files

- `leads.json` - Input dataset
- `deduplicate.php` - PHP CLI program to perform deduplication
- `output.json` - Sample output after deduplication
- `changelog.json` - Sample change log of all replacements
- `README.md` - This file

## How to Run

Make sure you have PHP installed.

```bash
php deduplicate.php
```

This will generate `output.json` and `changelog.json`.

