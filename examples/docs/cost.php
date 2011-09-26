<?php

include 'stream.php';

// Get the cost. This will compile the definition, so we catch potential
// errors from that.
$cost = array();
try {
	$cost = $definition->getCostBreakdown();
}
catch (DataSift_Exception_CompileFailed $e) {
	die("CSDL compilation failed: " . $e->getMessage() . "\n\n");
}

// Format the cost details for output in a table
$costtable = array();
$maxlength = array('target' => strlen('Target'), 'times used' => strlen('Times used'), 'complexity' => strlen('Complexity'));
foreach ($cost['costs'] as $tgt => $c) {
	$maxlength['target'] = max($maxlength['target'], strlen($tgt));
	$maxlength['times used'] = max($maxlength['times used'], strlen(number_format($c['count'])));
	$maxlength['complexity'] = max($maxlength['complexity'], strlen(number_format($c['cost'])));

	$costtable[] = array(
		'target' => $tgt,
		'times used' => number_format($c['count']),
		'complexity' => number_format($c['cost']),
	);

	foreach ($c['targets'] as $tgt => $d) {
		$maxlength['target'] = max($maxlength['target'], 2 + strlen($tgt));
		$maxlength['times used'] = max($maxlength['times used'], strlen(number_format($d['count'])));
		$maxlength['complexity'] = max($maxlength['complexity'], strlen(number_format($d['cost'])));

		$costtable[] = array(
			'target' => '  ' . $tgt,
			'times used' => number_format($d['count']),
			'complexity' => number_format($d['cost']),
		);
	}
}

$maxlength['complexity'] = max($maxlength['complexity'], strlen(number_format($cost['total'])));

echo "\n";
echo '/-' . str_repeat('-', $maxlength['target']) . '---';
echo str_repeat('-', $maxlength['times used']) . '---';
echo str_repeat('-', $maxlength['complexity']) . "-\\\n";

echo '| ' . str_pad('Target', $maxlength['target']) . ' | ';
echo str_pad('Times Used', $maxlength['times used']) . ' | ';
echo str_pad('Complexity', $maxlength['complexity']) . " |\n";

echo '|-' . str_repeat('-', $maxlength['target']) . '-+-';
echo str_repeat('-', $maxlength['times used']) . '-+-';
echo str_repeat('-', $maxlength['complexity']) . "-|\n";

foreach ($costtable as $row) {
	echo '| ' . str_pad($row['target'], $maxlength['target']) . ' | ';
	echo str_pad($row['times used'], $maxlength['times used'], ' ', STR_PAD_LEFT) . ' | ';
	echo str_pad($row['complexity'], $maxlength['complexity'], ' ', STR_PAD_LEFT) . " |\n";
}

echo '|-' . str_repeat('-', $maxlength['target']) . '---';
echo str_repeat('-', $maxlength['times used']) . '---';
echo str_repeat('-', $maxlength['complexity']) . "-|\n";

echo '| ' . str_repeat(' ', $maxlength['target'] + 3);
echo str_pad('Total', $maxlength['times used'], ' ', STR_PAD_LEFT) . ' = ';
echo str_pad($cost['total'], $maxlength['complexity'], ' ', STR_PAD_LEFT) . " |\n";

echo '\\-' . str_repeat('-', $maxlength['target']) . '---';
echo str_repeat('-', $maxlength['times used']) . '---';
echo str_repeat('-', $maxlength['complexity']) . "-/\n";

echo "\n";

if ($cost['total'] > 1000) {
	$tiernum = 3;
	$tierdesc = 'high complexity';
}
elseif ($cost['total'] > 100) {
	$tiernum = 2;
	$tierdesc = 'medium complexity';
}
else {
	$tiernum = 1;
	$tierdesc = 'simple complexity';
}

echo 'A total cost of ' . number_format($cost['total']) . ' puts this stream in tier ' . $tiernum . ', ' . $tierdesc . "\n\n";