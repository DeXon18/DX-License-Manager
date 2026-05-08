<?php

namespace App\Services\System;

use Illuminate\Support\Facades\File;

class ChangelogService
{
    /**
     * Path to the changelog file inside the container
     */
    protected string $path = '/var/www/management/CHANGELOG.md';

    /**
     * Parse the CHANGELOG.md file into a structured array
     */
    public function getParsedChangelog(): array
    {
        if (!File::exists($this->path)) {
            return [];
        }

        $content = File::get($this->path);
        $lines = explode("\n", $content);
        
        $entries = [];
        $currentDate = null;
        $currentCategory = null;

        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line) || $line === '---') {
                continue;
            }

            // Detect Date Headers: ## [2026-05-08] — Title
            if (preg_match('/^##\s*\[(\d{4}-\d{2}-\d{2})\]\s*—\s*(.*)/', $line, $matches)) {
                $currentDate = $matches[1];
                $title = $matches[2];
                
                $entries[$currentDate] = [
                    'date' => $currentDate,
                    'title' => $title,
                    'categories' => []
                ];
                $currentCategory = 'General'; // Default category if none specified
                continue;
            }

            if (!$currentDate) continue;

            // Detect Categories: ### Added, ### Fixed, etc.
            if (preg_match('/^###\s*(.*)/', $line, $matches)) {
                $currentCategory = trim($matches[1]);
                if (!isset($entries[$currentDate]['categories'][$currentCategory])) {
                    $entries[$currentDate]['categories'][$currentCategory] = [];
                }
                continue;
            }

            // Detect Signature
            if (preg_match('/^_Firmado por:\s*\*\*(.*)\*\*(.*)_/', $line, $matches)) {
                $entries[$currentDate]['signature'] = trim($matches[1] . $matches[2]);
                continue;
            }

            // Detect List Items: - Item
            if (preg_match('/^-\s*(.*)/', $line, $matches)) {
                $item = $matches[1];
                
                // Parse bold titles in items: **Title**: Description
                if (preg_match('/^\*\*(.*?)\*\*:\s*(.*)/', $item, $itemMatches)) {
                    $entries[$currentDate]['categories'][$currentCategory][] = [
                        'label' => $itemMatches[1],
                        'description' => $itemMatches[2]
                    ];
                } else {
                    $entries[$currentDate]['categories'][$currentCategory][] = [
                        'label' => null,
                        'description' => $item
                    ];
                }
            }
        }

        return $entries;
    }
}
