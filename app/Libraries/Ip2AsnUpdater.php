<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Libraries;

use App\Singletons\Ip2Asn;
use Log;

class Ip2AsnUpdater
{
    public static function getDbPath(string $version): string
    {
        return database_path("ip2asn/{$version}.tsv");
    }

    public static function getIndexPath(string $version): string
    {
        return database_path("ip2asn/{$version}.idx");
    }

    public function run(?callable $logger = null): void
    {
        foreach (Ip2Asn::IP as $version) {
            $prefixedLogger = function (string $message) use ($logger, $version): void {
                $prefixedMessage = "[{$version}] $message";

                if (isset($logger)) {
                    $logger($prefixedMessage);
                } else {
                    Log::info("ip2asn: {$prefixedMessage}");
                }
            };

            $this->update($version, $prefixedLogger);
        }
    }

    private function update(string $version, callable $logger): void
    {
        $logger('Checking db for updates');

        $dbPath = static::getDbPath($version);
        $indexPath = static::getIndexPath($version);

        $dbExists = file_exists($dbPath);

        $dbExpireTime = time() - (24 * 3600);
        $newDb = !$dbExists || filemtime($dbPath) < $dbExpireTime;

        $newIndex = !$dbExists || !file_exists($indexPath) || filemtime($dbPath) > filemtime($indexPath);
        if (!$newDb && !$newIndex) {
            $logger('All relevant files are up to date');

            return;
        }

        if ($newDb) {
            $logger('Db file is outdated. Downloading');
            $tsv = gzdecode(file_get_contents("https://iptoasn.com/data/ip2asn-{$version}.tsv.gz"));
        } else {
            $tsv = file_get_contents($dbPath);
        }

        $logger('Indexing db');
        $currentLine = 0;
        $index = pack('l', $currentLine);
        while (($currentLine = strpos($tsv, "\n", $currentLine)) !== false) {
            $currentLine++;
            if (isset($tsv[$currentLine])) {
                $index .= pack('l', $currentLine);
            }
        }

        $logger('Writing db and index to file');
        if ($newDb) {
            file_put_contents($dbPath, $tsv);
        }
        file_put_contents($indexPath, $index);

        $logger('Finished updating db');
    }
}
