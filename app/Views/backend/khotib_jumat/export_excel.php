<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Jadwal_Khotib_Jumat_Kuartal_" . $kuartalPilih . "_" . $tahunPilih . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo '<?xml version="1.0"?>' . "\n";
echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Calibri" ss:Size="11"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="Title">
   <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="Subtitle">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Italic="1"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="Header">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/>
   <Interior ss:Color="#D9EDF7" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="CellBorder">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="CellBorderCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="CellBorderWrapCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="CellBorderBold">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/>
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
  <Style ss:ID="CellBorderBoldCenter">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
 </Styles>

 <!-- SHEET 1: JADWAL (KODE NIS) -->
 <Worksheet ss:Name="Jadwal (Kode NIS)">
  <Table>
   <Column ss:Width="40"/>
   <Column ss:Width="200"/>
   <?php foreach($fridays as $f): ?>
    <Column ss:Width="60"/>
   <?php endforeach; ?>

   <Row ss:Height="24">
    <Cell ss:MergeAcross="<?= count($fridays) + 1 ?>" ss:StyleID="Title">
     <Data ss:Type="String">MATRIKS JADWAL KHOTIB JUMAT (KODE NIS)</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="<?= count($fridays) + 1 ?>" ss:StyleID="Subtitle">
     <Data ss:Type="String">KUARTAL <?= esc($kuartalPilih) ?> TAHUN <?= esc($tahunPilih) ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Header Row 1 -->
   <Row ss:Height="22">
    <Cell ss:MergeDown="1" ss:StyleID="Header"><Data ss:Type="String">NO</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="Header"><Data ss:Type="String">NAMA MASJID</Data></Cell>
    <?php
        $months = [];
        foreach ($fridays as $f) {
            $m = date('F', strtotime($f));
            if(!isset($months[$m])) $months[$m] = 0;
            $months[$m]++;
        }
    ?>
    <?php foreach ($months as $monthName => $colspan): ?>
     <Cell ss:MergeAcross="<?= $colspan - 1 ?>" ss:StyleID="Header"><Data ss:Type="String"><?= strtoupper($monthName) ?></Data></Cell>
    <?php endforeach; ?>
   </Row>

   <!-- Header Row 2 (Dates) -->
   <Row ss:Height="20">
    <Cell ss:Index="3" ss:StyleID="Header"><Data ss:Type="String"><?= date('d', strtotime($fridays[0])) ?></Data></Cell>
    <?php for($i=1; $i<count($fridays); $i++): ?>
     <Cell ss:StyleID="Header"><Data ss:Type="String"><?= date('d', strtotime($fridays[$i])) ?></Data></Cell>
    <?php endfor; ?>
   </Row>

   <!-- Data Rows -->
   <?php if(empty($masjidList)): ?>
    <Row>
     <Cell ss:MergeAcross="<?= count($fridays) + 1 ?>" ss:StyleID="CellBorderCenter">
      <Data ss:Type="String">Belum ada penugasan Khotib Jumat pada Kuartal ini.</Data>
     </Cell>
    </Row>
   <?php else: ?>
    <?php $no = 1; foreach ($masjidList as $m): ?>
     <Row ss:Height="20">
      <Cell ss:StyleID="CellBorderCenter"><Data ss:Type="Number"><?= $no++ ?></Data></Cell>
      <Cell ss:StyleID="CellBorderBold"><Data ss:Type="String"><?= esc($m['nama']) ?></Data></Cell>
      <?php foreach ($fridays as $tgl): ?>
       <?php 
           $personilId = $matrixIds[$m['id_masjid_mushola']][$tgl] ?? null;
           $nisDisplay = '-';
           if ($personilId && isset($personilMap[$personilId])) {
               $nisDisplay = $personilMap[$personilId]['nia'] ?: $personilMap[$personilId]['nama_lengkap'];
           }
       ?>
       <Cell ss:StyleID="CellBorderCenter"><Data ss:Type="String"><?= esc($nisDisplay) ?></Data></Cell>
      <?php endforeach; ?>
     </Row>
    <?php endforeach; ?>
   <?php endif; ?>
  </Table>
 </Worksheet>

 <!-- SHEET 2: JADWAL (NAMA & HP) -->
 <Worksheet ss:Name="Jadwal (Nama &amp; HP)">
  <Table>
   <Column ss:Width="40"/>
   <Column ss:Width="200"/>
   <?php foreach($fridays as $f): ?>
    <Column ss:Width="170"/>
   <?php endforeach; ?>

   <Row ss:Height="24">
    <Cell ss:MergeAcross="<?= count($fridays) + 1 ?>" ss:StyleID="Title">
     <Data ss:Type="String">MATRIKS JADWAL KHOTIB JUMAT (NAMA &amp; NO. HP)</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="<?= count($fridays) + 1 ?>" ss:StyleID="Subtitle">
     <Data ss:Type="String">KUARTAL <?= esc($kuartalPilih) ?> TAHUN <?= esc($tahunPilih) ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Header Row 1 -->
   <Row ss:Height="22">
    <Cell ss:MergeDown="1" ss:StyleID="Header"><Data ss:Type="String">NO</Data></Cell>
    <Cell ss:MergeDown="1" ss:StyleID="Header"><Data ss:Type="String">NAMA MASJID</Data></Cell>
    <?php foreach ($months as $monthName => $colspan): ?>
     <Cell ss:MergeAcross="<?= $colspan - 1 ?>" ss:StyleID="Header"><Data ss:Type="String"><?= strtoupper($monthName) ?></Data></Cell>
    <?php endforeach; ?>
   </Row>

   <!-- Header Row 2 (Dates) -->
   <Row ss:Height="20">
    <Cell ss:Index="3" ss:StyleID="Header"><Data ss:Type="String"><?= date('d', strtotime($fridays[0])) ?></Data></Cell>
    <?php for($i=1; $i<count($fridays); $i++): ?>
     <Cell ss:StyleID="Header"><Data ss:Type="String"><?= date('d', strtotime($fridays[$i])) ?></Data></Cell>
    <?php endfor; ?>
   </Row>

   <!-- Data Rows -->
   <?php if(empty($masjidList)): ?>
    <Row>
     <Cell ss:MergeAcross="<?= count($fridays) + 1 ?>" ss:StyleID="CellBorderCenter">
      <Data ss:Type="String">Belum ada penugasan Khotib Jumat pada Kuartal ini.</Data>
     </Cell>
    </Row>
   <?php else: ?>
    <?php $no = 1; foreach ($masjidList as $m): ?>
     <Row ss:Height="36">
      <Cell ss:StyleID="CellBorderCenter"><Data ss:Type="Number"><?= $no++ ?></Data></Cell>
      <Cell ss:StyleID="CellBorderBold"><Data ss:Type="String"><?= esc($m['nama']) ?></Data></Cell>
      <?php foreach ($fridays as $tgl): ?>
       <?php 
           $personilId = $matrixIds[$m['id_masjid_mushola']][$tgl] ?? null;
           $cellContent = '-';
           if ($personilId && isset($personilMap[$personilId])) {
               $pNia = esc($personilMap[$personilId]['nia'] ?: '');
               $pName = esc($personilMap[$personilId]['nama_lengkap']);
               $pHp = esc($personilMap[$personilId]['no_hp'] ?: '');
               
               $headerText = ($pNia ? $pNia . ' - ' : '') . $pName;
               $cellContent = $headerText . ($pHp ? "&#10;" . $pHp : "");
           }
       ?>
       <Cell ss:StyleID="CellBorderWrapCenter"><Data ss:Type="String"><?= $cellContent ?></Data></Cell>
      <?php endforeach; ?>
     </Row>
    <?php endforeach; ?>
   <?php endif; ?>
  </Table>
 </Worksheet>

 <!-- SHEET 3: DAFTAR KHOTIB -->
 <Worksheet ss:Name="Daftar Khotib">
  <Table>
   <Column ss:Width="40"/>
   <Column ss:Width="140"/>
   <Column ss:Width="260"/>
   <Column ss:Width="160"/>

   <Row ss:Height="24">
    <Cell ss:MergeAcross="3" ss:StyleID="Title">
     <Data ss:Type="String">DAFTAR KHOTIB / MUBALIGH</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="3" ss:StyleID="Subtitle">
     <Data ss:Type="String">KUARTAL <?= esc($kuartalPilih) ?> TAHUN <?= esc($tahunPilih) ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>

   <Row ss:Height="22">
    <Cell ss:StyleID="Header"><Data ss:Type="String">NO</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NO. NIS / NIA</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NAMA KHOTIB / MUBALIGH</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NO. HP / WHATSAPP</Data></Cell>
   </Row>

   <?php if(empty($personils)): ?>
    <Row>
     <Cell ss:MergeAcross="3" ss:StyleID="CellBorderCenter">
      <Data ss:Type="String">Belum ada data Khotib pada Kuartal ini.</Data>
     </Cell>
    </Row>
   <?php else: ?>
    <?php $noP = 1; foreach ($personils as $p): ?>
     <Row ss:Height="20">
      <Cell ss:StyleID="CellBorderCenter"><Data ss:Type="Number"><?= $noP++ ?></Data></Cell>
      <Cell ss:StyleID="CellBorderBoldCenter"><Data ss:Type="String"><?= esc($p['nia'] ?: '-') ?></Data></Cell>
      <Cell ss:StyleID="CellBorder"><Data ss:Type="String"><?= esc($p['nama_lengkap']) ?></Data></Cell>
      <Cell ss:StyleID="CellBorderCenter"><Data ss:Type="String"><?= esc($p['no_hp'] ?: '-') ?></Data></Cell>
     </Row>
    <?php endforeach; ?>
   <?php endif; ?>
  </Table>
 </Worksheet>
</Workbook>
