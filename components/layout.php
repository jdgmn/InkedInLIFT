<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'LIFT' ?></title>
    <link rel="stylesheet" type="text/css" href="css/all.css">
</head>
<body>
    <?php include 'components/navbar.php'; ?> 

    <main>
        <div class="section">
            <?= $content ?? '' ?>
        </div> 
    </main>
</body>
</html>