<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'LIFT' ?></title>
    <link rel="stylesheet" type="text/css" href="css/all.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'components/navbar.php'; ?> 
    <main>
        <div class="section-center">
            <div class="section">
                <?= $content ?? '' ?>
            </div> 
        </div>
    </main>
    <?php include 'components/footer.php'; ?>
</body>
</html>