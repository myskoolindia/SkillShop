<?php
$planKey   = 'skillvation';
$planLabel = 'Skillvation lab';
?>
<?php echo view('cart/_plan_cart', array_merge(
    get_defined_vars(),
    ['planKey' => $planKey, 'planLabel' => $planLabel]
)); ?>
