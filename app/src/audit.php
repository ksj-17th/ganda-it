<?php

function log_clean(mixed $value): string {
    return str_replace(["\r", "\n"], ['\\r', '\\n'], (string)$value);
}

function request_id(): string {
    return $_SERVER['HTTP_X_REQUEST_ID'] ?? '-';
}

function client_ip(): string {
    return $_SERVER['REMOTE_ADDR'] ?? '-';
}

function write_mft_log(string $component, string $line): void {
    $map = [
        'WEB' => '/var/log/mft/DMZ_WEB.log',
        'ISAPI' => '/var/log/mft/DMZ_ISAPI.log',
        'WEBAPI' => '/var/log/mft/DMZ_WebAPI.log',
    ];
    if (!isset($map[$component])) return;
    @file_put_contents($map[$component], $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function write_syslog_event(string $line): void {
    // Local rsyslogd inside the PHP/MFT container receives this via /dev/log.
    openlog('mft', LOG_PID, LOG_LOCAL0);
    syslog(LOG_INFO, $line);
    closelog();
}

function audit_event(
    string $component,
    string $event,
    string $username = '-',
    string $target = '-',
    bool $success = true,
    array $extra = [],
    bool $writeDb = true
): void {
    $parts = [
        date('c'),
        'component=' . log_clean($component),
        'event=' . log_clean($event),
        'request_id=' . log_clean(request_id()),
        'src_ip=' . log_clean(client_ip()),
        'user=' . log_clean($username),
        'target="' . log_clean($target) . '"',
        'result=' . ($success ? 'success' : 'failure'),
    ];

    foreach ($extra as $k => $v) {
        $parts[] = log_clean($k) . '="' . log_clean($v) . '"';
    }

    $line = implode(' ', $parts);
    write_mft_log($component, $line);
    write_syslog_event($line);

    if (!$writeDb) return;

    try {
        $st = db()->prepare(
            'INSERT INTO audit_logs
             (request_id,username,event_type,target,remote_ip,success,detail)
             VALUES (?,?,?,?,?,?,?)'
        );
        $st->execute([
            request_id(),
            $username,
            $event,
            $target,
            client_ip(),
            $success ? 1 : 0,
            json_encode($extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    } catch (Throwable $e) {
        // Product operation must continue even if audit persistence fails.
        error_log('audit persistence failed: ' . $e->getMessage());
    }
}
