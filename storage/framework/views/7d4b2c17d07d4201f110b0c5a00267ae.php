<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['dark' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['dark' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="brand <?php echo e($dark ? 'dark' : ''); ?>">
    <span class="brand-logo-frame">
        <img class="brand-logo" src="/assets/catalyst-logo.png" alt="Catalyst Summit logo" onerror="this.onerror=null;this.src='/assets/asset-fallback.svg'" />
    </span>
    <span>Catalyst<br />Summit</span>
</div>
<?php /**PATH C:\Users\Ryan\Documents\catalyst-summit\resources\views/components/brand.blade.php ENDPATH**/ ?>