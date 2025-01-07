<?php
session_start();
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MicroRobots </title>
</head>
<body>
<form method="post">
    <label> Fila inicial: </label> <input type="number" name="initial_row" min="1" max="6" required> <br>
    <label> Columna inicial: </label> <input type="number" name="initial_column" min="1" max="6" required> <br>
    <label> Fila final: </label> <input type="number" name="final_row" min="1" max="6" required> <br>
    <label> Columna final: </label> <input type="number" name="final_column" min="1" max="6" required> <br>
    <button type="submit" name="validate"> review move </button>
    <button type="submit" name="restart"> restart game </button>
</form>

<?php

function generate_combinations() {
    $numeros = [1, 2, 3, 4, 5, 6];
    $colores = ["red", "blue", "green", "yellow", "purple", "orange"];
    $combinaciones = [];

    foreach ($numeros as $numero) {
        foreach ($colores as $color) {
            $combinaciones[] = ["numero" => $numero, "color" => $color];
        }
    }

    return $combinaciones;
}

function create_board($combinaciones) {
    $indices = range(0, count($combinaciones) - 1);
    shuffle($indices);

    $grid = [];
    for ($i = 0; $i < 6; $i++) {
        $grid[] = array_slice($indices, $i * 6, 6);
    }

    return $grid;
}

function display_board($grid, $combinaciones) {
    echo "<table border='6' style='text-align: center'>";
    foreach ($grid as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            $combinacion = $combinaciones[$cell];
            echo "<td>{$combinacion['numero']} {$combinacion['color']}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

function check_combination($inicio, $fin, $combinaciones) {
    $inicio_comb = $combinaciones[$inicio];
    $fin_comb = $combinaciones[$fin];

    return $inicio_comb['numero'] == $fin_comb['numero'] || $inicio_comb['color'] == $fin_comb['color'];
}

function check_move($initial_row, $initial_column, $final_row, $final_column) {
    return $initial_row == $final_row || $initial_column == $final_column;
}

if (!isset($_SESSION['grid'])) {
    $_SESSION['combinaciones'] = generate_combinations();
    $_SESSION['grid'] = create_board($_SESSION['combinaciones']);
}

$combinaciones = $_SESSION['combinaciones'];
$grid = $_SESSION['grid'];

$initial_row = $initial_column = $final_row = $final_column = null;
$start = $end = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['restart'])) {
        $_SESSION['combinaciones'] = generate_combinations();
        $_SESSION['grid'] = create_board($_SESSION['combinaciones']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['validate'])) {
        $initial_row = (int)$_POST['initial_row'] - 1; 
        $initial_column = (int)$_POST['initial_column'] - 1;
        $final_row = (int)$_POST['final_row'] - 1;
        $final_column = (int)$_POST['final_column'] - 1;

        if (!isset($grid[$initial_row][$initial_column]) || !isset($grid[$final_row][$final_column])) {
            echo "<p>Move out of range.</p>";
        } else {
            $start = $grid[$initial_row][$initial_column];
            $end = $grid[$final_row][$final_column];

            if (check_move($initial_row, $initial_column, $final_row, $final_column)) {
                if (check_combination($start, $end, $combinaciones)) {
                    echo "<p>Movimiento Valido.</p>";
                } else {
                    echo "<p>Movimiento Invalido.</p>";
                }
            } else {
                echo "<p>Movimiento no Permitido.</p>";
            }
        }
    }
}

if ($start !== null && $end !== null) {
    echo "<p>Fila inicial: {$initial_row}, Columna inicial: {$initial_column}</p>";
    echo "<p>Fila final: {$final_row}, Columna final: {$final_column}</p>";
    echo "<p>Combinacion Inicial: Numero: {$combinaciones[$start]['numero']} - Color: {$combinaciones[$start]['color']}</p>";
    echo "<p>Combinacion Final: Numero: {$combinaciones[$end]['numero']} - Color: {$combinaciones[$end]['color']}</p>";
}

display_board($grid, $combinaciones);
?>
<?php
ob_end_flush();
?>
