<?php
require __DIR__ . '/../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

const DAILY_LIMIT = 5;
$barcodeImage     = '';
$barcode          = '';
$displayText      = '';
$size             = '';
$color            = '';
$error            = '';
$remaining        = DAILY_LIMIT;

$today = date('Y-m-d');

//Read today's usage from cookie
$usage = 0;

if (!empty($_COOKIE['barcode_usage'])) {
    $parts = explode('|', $_COOKIE['barcode_usage']);

    if (count($parts) === 2 && $parts[0] === $today) {
        $usage = max(0, (int) $parts[1]);
    }
}

$remaining = max(0, DAILY_LIMIT - $usage);

// Handle barcode generation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode     = trim($_POST['barcode'] ?? '');
    $displayText = trim($_POST['display_text'] ?? '');
    $size        = trim($_POST['size'] ?? '');
    $color       = trim($_POST['color'] ?? '');

    // Check daily limit

    if ($usage >= DAILY_LIMIT) {
        $error = 'You have reached your daily limit of 5 barcode prints.';
    } elseif ($barcode === '') {
        $error = 'Please enter a barcode value.';
    } else {
        // Generate barcode
        $g = new BarcodeGeneratorPNG();

        $barcodeImage = base64_encode(
            $g->getBarcode($barcode, $g::TYPE_CODE_128, 2, 50)
        );

        //Count successful barcode generation
        $usage++;

        $remaining = max(0, DAILY_LIMIT - $usage);

        // Store today's usage in cookie. Cookie expires at midnight.
        setcookie(
            'barcode_usage',
            $today . '|' . $usage,
            [
                'expires'  => strtotime('tomorrow'),
                'path'     => '/',
                'httponly' => true,
                'secure'   => true,
                'samesite' => 'Lax',
            ]
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Barcode Generator</title>
    <link rel="stylesheet" href="/style/style.css">
</head>

<body>
    <div class="container">
        <h2>Barcode Generator</h2>

        <!-- Daily limit -->
        <div class="limit">
            Daily barcode prints remaining:
            <strong><?= $remaining ?></strong>/<?= DAILY_LIMIT ?>
        </div>


        <!-- Error -->
        <?php if ($error): ?>
            <div class="error"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>


        <!-- Form -->
        <?php if ($remaining > 0): ?>
            <form method="POST">
                <div class="row">
                    <label>Barcode Value</label>
                    <input type="text" name="barcode" value="<?= htmlspecialchars($barcode) ?>" minlength="3" maxlength="10" required autofocus>
                </div>


                <div class="row">
                    <label>Display Text</label>
                    <input type="text" name="display_text" value="<?= htmlspecialchars($displayText) ?>" maxlength="10">
                </div>

                <div class="row">
                    <label>Display Size</label>
                    <input type="text" name="size" value="<?= htmlspecialchars($size) ?>" maxlength="6">
                </div>

                <div class="row">
                    <label>Display Color</label>
                    <input type="text" name="color" value="<?= htmlspecialchars($color) ?>" maxlength="8">
                </div>

                <button type="submit"> Generate Barcode </button>
            </form>

        <?php endif; ?>


        <!-- Generated barcode -->
        <?php if ($barcodeImage): ?>
            <div class="label-wrapper">

                <div class="label">

                    <img src="data:image/png;base64,<?= $barcodeImage ?>" alt="Barcode">

                    <div class="middle"><?= htmlspecialchars($displayText) ?></div>

                    <div class="bottom">
                        <span> <?= htmlspecialchars($size) ?></span>
                        <span><?= htmlspecialchars($color) ?></span>
                    </div>
                </div>


                <div class="print" style="margin-top: 10px;">
                    <button onclick="window.print()">Print</button>
                </div>
            </div>
        <?php endif; ?>

    </div>

</body>

</html>