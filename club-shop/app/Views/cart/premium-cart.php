<?php
$planKey   = 'premium';
$planLabel = 'Premium';
?>
<?php echo view('cart/_plan_cart', array_merge(
    get_defined_vars(),
    ['planKey' => $planKey, 'planLabel' => $planLabel]
)); ?>
