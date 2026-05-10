<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wolfstreet77</title>
    <link rel="stylesheet" href="/assets/css/base/reset.css" />
    <link rel="stylesheet" href="/assets/css/layout/grid.css" />
    <link rel="stylesheet" href="/assets/css/components/card.css" />
    <link rel="stylesheet" href="/assets/css/pages/dashboard.css" />
    <link rel="stylesheet" href="/assets/css/themes/dark.css" />
    <link rel="stylesheet" href="/assets/css/animations/transition.css" />
</head>
<body>
    <div id="app">
        <?php echo $content ?? ''; ?>
    </div>
    <script type="module" src="/assets/js/core/app.js"></script>
</body>
</html>
