<?php

declare(strict_types=1);

$data = [];

$locations = ['/', '.', '../../..'];
foreach ($locations as $location) {
    $item = [];
    $item['free'] = disk_free_space($location);
    $item['total'] = disk_total_space($location);
    $item['used'] = $item['total'] - $item['free'];
    $item['percentage'] = number_format(($item['used'] / $item['total']) * 100, 1) . '%';

    $data[$location] = [
        $location,
        number_format($item['free'] / 1048576, 2) . ' MB <br> ' . number_format($item['free'] / 1073741824, 2) . ' GB',
        number_format($item['used'] / 1048576, 2) . ' MB <br> ' . number_format($item['used'] / 1073741824, 2) . ' GB',
        number_format($item['total'] / 1048576, 2) . ' MB <br> ' . number_format($item['total'] / 1073741824, 2) . ' GB',
        $item['percentage'],
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disk space info</title>
    <style>
        table,
        th,
        td {
            border: 1px solid #000000;
        }

        td {
            padding: 1rem;
        }
    </style>
</head>

<body>
<table>
    <tr>
        <th></th>
        <th>Free</th>
        <th>Used</th>
        <th>Total</th>
        <th>Percent</th>
    </tr>
    <?php foreach ($data as $key => $value) { ?>
        <tr>
            <?php foreach ($value as $k => $v) { ?>
                <td><?php echo $v; ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>
</body>

</html>
