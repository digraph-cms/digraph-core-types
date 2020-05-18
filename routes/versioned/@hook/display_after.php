<?php
if ($currentVersion = $package->noun()->currentVersion()) {
    $package['response.last-modified'] = $currentVersion['dso.modified.date'];
}
