<?php
// Detect if we're in admin folder for CSS path
$in_admin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$css_path = $in_admin ? '../assets/css/style.css' : 'assets/css/style.css';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($description) ? htmlspecialchars($description) : 'Kids Activity Books - Creative Learning Store'; ?>">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Kids Activity Books - Creative Learning Store'; ?></title>
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>