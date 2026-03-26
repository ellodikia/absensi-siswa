<?php
require '../vendor/autoload.php'; 
include '../include/config.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 
use PhpOffice\PhpSpreadsheet\Style\Alignment; 
use PhpOffice\PhpSpreadsheet\Style\Border; 

if ($_SESSION['role'] != 'guru') { 
    header("Location: ../login.php"); 
    exit; 
}

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$kelas_filter = $_GET['kelas'] ?? ''; 

$jml_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); 
$nama_bulan = date('F', mktime(0, 0, 0, $bulan, 1)); 

$spreadsheet = new Spreadsheet(); 
$sheet = $spreadsheet->getActiveSheet();

$title = "REKAP ABSENSI - $nama_bulan $tahun";
$sheet->setCellValue('A1', $title);
$sheet->mergeCells("A1:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($jml_hari + 1) . "1");
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$info_kelas = ($kelas_filter != '') ? "KELAS: $kelas_filter" : "SEMUA KELAS";
$sheet->setCellValue('A2', $info_kelas);
$sheet->mergeCells("A2:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($jml_hari + 1) . "2");
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A3', 'NAMA SISWA');
$sheet->getStyle('A3')->getFont()->setBold(true);

for ($d = 1; $d <= $jml_hari; $d++) {
    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($d + 1);
    $sheet->setCellValue($col . '3', $d);
    $sheet->getStyle($col . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($col . '3')->getFont()->setBold(true);
}

$query_str = "SELECT id, nama_lengkap FROM siswa";
if ($kelas_filter != '') {
    $query_str .= " WHERE kelas_jurusan = '" . mysqli_real_escape_string($conn, $kelas_filter) . "'";
}
$query_str .= " ORDER BY nama_lengkap ASC";
$siswa_query = mysqli_query($conn, $query_str);

$rowNum = 4;
while ($s = mysqli_fetch_assoc($siswa_query)) {
    $sheet->setCellValue('A' . $rowNum, strtoupper($s['nama_lengkap'])); 
    
    for ($d = 1; $d <= $jml_hari; $d++) {
        $tgl = "$tahun-$bulan-" . sprintf('%02d', $d); 
        $q = mysqli_query($conn, "SELECT status FROM absensi WHERE siswa_id='" . $s['id'] . "' AND tanggal='$tgl'"); 
        $res = mysqli_fetch_assoc($q);
        $status = $res['status'] ?? ''; 
        
        $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($d + 1);
        $cell = $col . $rowNum;
        
        $val = $status ? strtoupper(substr($status, 0, 1)) : '-'; 
        $sheet->setCellValue($cell, $val);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($status == 'hadir') {
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF10B981');
        } elseif ($status == 'sakit' || $status == 'izin') {
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFF59E0B');
        } elseif ($status == 'alpha') {
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFEF4444');
        }
    }
    $rowNum++;
}

$lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($jml_hari + 1);
$range = "A3:$lastCol" . ($rowNum - 1);
$sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); 
$sheet->getColumnDimension('A')->setAutoSize(true); 

$filename = "Rekap_" . ($kelas_filter ?: 'Semua') . "_$nama_bulan.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output'); 
exit;