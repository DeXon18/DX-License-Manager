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
        $currentEntry = null;
        $currentCategory = null;

        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line) || $line === '---') {
                continue;
            }

            // Detect Date Headers: ## [2026-05-08] — Title
            if (preg_match('/^##\s*\[(\d{4}-\d{2}-\d{2})\]\s*—\s*(.*)/', $line, $matches)) {
                if ($currentEntry) {
                    $entries[] = $currentEntry;
                }

                $currentEntry = [
                    'date' => $matches[1],
                    'title' => trim($matches[2]),
                    'categories' => []
                ];
                $currentCategory = 'General';
                continue;
            }

            if (!$currentEntry) continue;

            // Detect Categories: ### Added, ### Fixed, etc.
            if (preg_match('/^###\s*(.*)/', $line, $matches)) {
                $currentCategory = trim($matches[1]);
                if (!isset($currentEntry['categories'][$currentCategory])) {
                    $currentEntry['categories'][$currentCategory] = [];
                }
                continue;
            }

            // Detect Signature
            if (preg_match('/^_Firmado por:\s*\*\*(.*)\*\*(.*)_/', $line, $matches)) {
                $currentEntry['signature'] = trim($matches[1] . $matches[2]);
                continue;
            }

            // Detect List Items: - Item
            if (preg_match('/^-\s*(.*)/', $line, $matches)) {
                $item = $matches[1];
                
                $tag = null;
                // Detect [TAG] at the start of the description
                if (preg_match('/^\[(.*?)\]\s*(.*)/', $item, $tagMatches)) {
                    $tag = $tagMatches[1];
                    $item = $tagMatches[2];
                }

                // Parse bold titles in items: **Title**: Description
                if (preg_match('/^\*\*(.*?)\*\*:\s*(.*)/', $item, $itemMatches)) {
                    $currentEntry['categories'][$currentCategory][] = [
                        'label' => $itemMatches[1],
                        'tag' => $tag,
                        'description' => $itemMatches[2]
                    ];
                } else {
                    $currentEntry['categories'][$currentCategory][] = [
                        'label' => null,
                        'tag' => $tag,
                        'description' => $item
                    ];
                }
            }
        }

        // Add last entry
        if ($currentEntry) {
            $entries[] = $currentEntry;
        }

        return $entries;
    }
}
