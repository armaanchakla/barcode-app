<?php
require __DIR__ . '/../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

$barcodeImage = '';
$barcode      = '';
$displayText  = '';
$size         = '';
$color        = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode     = trim($_POST['barcode'] ?? '');
    $displayText = trim($_POST['display_text'] ?? '');
    $size        = trim($_POST['size'] ?? '');
    $color       = trim($_POST['color'] ?? '');
    if ($barcode !== '') {
        $g            = new BarcodeGeneratorPNG();
        $barcodeImage = base64_encode($g->getBarcode($barcode, $g::TYPE_CODE_128, 2, 50));
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
        <form method="POST">
            <div class="row">
                <label>Barcode Value</label><input type="text" name="barcode" value="<?= htmlspecialchars($barcode) ?>" minlength="3" maxlength="10" required autofocus>
            </div>
            <div class="row">
                <label>Display Text</label><input type="text" name="display_text" value="<?= htmlspecialchars($displayText) ?>" maxlength="10">
            </div>
            <div class="row">
                <label>Display Size</label><input type="text" name="size" value="<?= htmlspecialchars($size) ?>" maxlength="6">
            </div>
            <div class="row">
                <label>Display Color</label><input type="text" name="color" value="<?= htmlspecialchars($color) ?>" maxlength="8">
            </div>
            <button type="submit">Generate Barcode</button>
        </form>
        <?php if ($barcodeImage): ?>
            <div class="label-wrapper">
                <div class="label">
                    <img src="data:image/png;base64,<?= $barcodeImage ?>" alt="Barcode">
                    <div class="middle"><?= htmlspecialchars($displayText) ?></div>
                    <div class="bottom"><span><?= htmlspecialchars($size) ?></span><span><?= htmlspecialchars($color) ?></span></div>
                </div>
                <div class="print" style="margin-top: 10px;"><button onclick="window.print()">Print</button></div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>