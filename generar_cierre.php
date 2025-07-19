<?php
require('fpdf/fpdf.php');
include 'Conexion_db.php';

if (!isset($_GET['id'])) {
    die("ID de orden no proporcionado.");
}

$orden_id = intval($_GET['id']);

// Obtener datos del buque y orden
$query = "
    SELECT b.nombre AS buque_nombre, b.matricula, b.tipo, b.fecha_entrada, b.fecha_salida, b.estado AS estado_buque,
           o.codigo AS codigo_orden, o.descripcion_trabajo, o.fecha_inicio, o.fecha_fin, o.observaciones AS observaciones_orden
    FROM ordenes_trabajo o
    JOIN buques b ON o.buque_id = b.id
    WHERE o.id = $orden_id
";
$res = mysqli_query($conexion, $query);
$data = mysqli_fetch_assoc($res);

// Obtener materiales utilizados
$query_materiales = "
    SELECT m.codigo, m.descripcion, mu.cantidad, mu.fecha_uso, p.nombre, p.apellidos, mu.observaciones
    FROM materiales_utilizados mu
    JOIN materiales m ON mu.material_id = m.id
    JOIN personal p ON mu.responsable_id = p.id
    WHERE mu.orden_id = $orden_id
";
$materiales = mysqli_query($conexion, $query_materiales);

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Título
$pdf->Cell(0, 10, 'Cierre de Buque - Orden de Trabajo', 0, 1, 'C');
$pdf->Ln(5);

// Información del buque
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Datos del Buque', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(95, 8, "Nombre: " . $data['buque_nombre'], 0);
$pdf->Cell(95, 8, "Matrícula: " . $data['matricula'], 0);
$pdf->Ln();
$pdf->Cell(95, 8, "Tipo: " . $data['tipo'], 0);
$pdf->Cell(95, 8, "Estado: " . $data['estado_buque'], 0);
$pdf->Ln();
$pdf->Cell(95, 8, "Fecha Entrada: " . $data['fecha_entrada'], 0);
$pdf->Cell(95, 8, "Fecha Salida: " . $data['fecha_salida'], 0);
$pdf->Ln(10);

// Información de la orden
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Orden de Trabajo', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(95, 8, "Código: " . $data['codigo_orden'], 0);
$pdf->Cell(95, 8, "Fecha Inicio: " . $data['fecha_inicio'], 0);
$pdf->Ln();
$pdf->MultiCell(0, 8, "Descripción: " . $data['descripcion_trabajo'], 0);
$pdf->Ln();
$pdf->MultiCell(0, 8, "Observaciones: " . ($data['observaciones_orden'] ?: 'Ninguna'), 0);
$pdf->Ln(5);

// Materiales utilizados
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Materiales Utilizados', 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 8, 'Código', 1);
$pdf->Cell(60, 8, 'Descripción', 1);
$pdf->Cell(20, 8, 'Cantidad', 1);
$pdf->Cell(30, 8, 'Fecha Uso', 1);
$pdf->Cell(45, 8, 'Responsable', 1);
$pdf->Ln();
$pdf->SetFont('Arial', '', 9);

while ($mat = mysqli_fetch_assoc($materiales)) {
    $pdf->Cell(30, 8, $mat['codigo'], 1);
    $pdf->Cell(60, 8, $mat['descripcion'], 1);
    $pdf->Cell(20, 8, $mat['cantidad'], 1);
    $pdf->Cell(30, 8, $mat['fecha_uso'], 1);
    $pdf->Cell(45, 8, $mat['nombre'] . " " . $mat['apellidos'], 1);
    $pdf->Ln();
    if (!empty($mat['observaciones'])) {
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->MultiCell(185, 6, "Observaciones: " . $mat['observaciones'], 1);
        $pdf->SetFont('Arial', '', 9);
    }
}

// Salida
$pdf->Output("I", "cierre_buque_" . $orden_id . ".pdf");
