<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <title><?php echo e(config('app.name', 'Catalyst Summit 2026')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js']); ?>
</head>
<body>
    <?php echo $__env->yieldContent('content'); ?>
</body>
</html>
<?php /**PATH C:\Users\Ryan\Documents\catalyst-summit\resources\views/layouts/app.blade.php ENDPATH**/ ?>