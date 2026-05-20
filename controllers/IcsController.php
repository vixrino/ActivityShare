<?php
class IcsController {

    /**
     * Génère un fichier .ics (RFC 5545) pour une activité, importable
     * dans Google Calendar / Apple Calendar / Outlook.
     */
    public function export() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $activityModel = new Activity();
        $activite = $activityModel->find($id);

        if (!$activite) {
            http_response_code(404);
            echo 'Activité introuvable.';
            return;
        }

        $start = strtotime($activite['date_debut']);
        $end = strtotime($activite['date_fin']);
        if (!$end || $end < $start) {
            $end = $start + 3600;
        }

        $fmt = function ($timestamp) {
            return gmdate('Ymd\THis\Z', $timestamp);
        };

        $titre = $this->escape($activite['titre']);
        $description = $this->escape(strip_tags($activite['description'] ?? ''));
        $lieu = $this->escape(trim(($activite['lieu'] ?? '') . ' ' . ($activite['adresse'] ?? '')));
        $organisateur = $this->escape(($activite['organisateur_prenom'] ?? '') . ' ' . ($activite['organisateur_nom'] ?? ''));
        $uid = 'activite-' . intval($activite['id']) . '@activityshare';
        $url = $this->absoluteUrl('index.php?page=activite&id=' . $activite['id']);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//ActivityShare//FR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $fmt(time()),
            'DTSTART:' . $fmt($start),
            'DTEND:' . $fmt($end),
            'SUMMARY:' . $titre,
            'DESCRIPTION:' . $description . '\\n\\nOrganisé par ' . $organisateur . '\\n' . $url,
            'LOCATION:' . $lieu,
            'URL:' . $url,
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        $ics = implode("\r\n", $lines) . "\r\n";

        $filename = 'activite-' . preg_replace('/[^a-z0-9_-]/i', '-', $activite['titre']) . '.ics';
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($ics));
        echo $ics;
        exit;
    }

    /**
     * Échappement spécifique au format iCal : \, , ; \n
     */
    private function escape($text) {
        $text = (string)$text;
        $text = str_replace(['\\', "\r\n", "\n", ',', ';'], ['\\\\', '\\n', '\\n', '\\,', '\\;'], $text);
        return $text;
    }

    private function absoluteUrl($path) {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $dir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        return $scheme . '://' . $host . $dir . '/' . ltrim($path, '/');
    }
}
