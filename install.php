<?php
// This repository now uses the Index app flow as its active entrypoint.
// The legacy installer templates no longer exist, so route to the working app.
header('Location: /Index/index.php');
exit;
