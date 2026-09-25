<?php
$planKey   = 'advance';
$planLabel = 'Advance';
?>
<?php echo view('cart/_plan_cart', array_merge(
    get_defined_vars(),
    ['planKey' => $planKey, 'planLabel' => $planLabel]
)); ?>
