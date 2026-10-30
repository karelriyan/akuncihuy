<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/config/database.php';

function out($data, int $code=200): void {
    http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit;
}
function body(): array {
    $raw=file_get_contents('php://input'); $v=json_decode($raw,true);
    return is_array($v)?$v:$_POST;
}
function strv($v): string { return trim((string)($v ?? '')); }
$action = $_GET['action'] ?? '';
$input = body();

try {
 switch ($action) {
  case 'dashboard':
    $groups=$pdo->query("SELECT kelompok_akun, SUM(saldo_referensi) total FROM coa GROUP BY kelompok_akun")->fetchAll();
    $s=[]; foreach($groups as $g){$s[$g['kelompok_akun']]=(float)$g['total'];}
    $counts=[];
    foreach(['barang'=>'barang','pelanggan'=>'pelanggan','pemasok'=>'pemasok','karyawan'=>'karyawan'] as $k=>$t){$counts[$k]=(int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();}
    $journalCount=(int)$pdo->query("SELECT COUNT(*) FROM jurnal_umum WHERE status='Posted'")->fetchColumn();
    $revenue=$s['Pendapatan']??0; $hpp=$s['HPP']??0; $expense=$s['Beban Operasional']??0;
    out(['summary'=>[
      'aset'=>$s['Aset']??0,'liabilitas'=>$s['Liabilitas']??0,'ekuitas'=>$s['Ekuitas']??0,
      'pendapatan'=>$revenue,'hpp'=>$hpp,'beban'=>$expense,'laba_kotor'=>$revenue-$hpp,
      'laba_bersih'=>$revenue-$hpp-$expense
    ],'counts'=>$counts,'journal_count'=>$journalCount]);
  case 'list':
    $type=$_GET['type']??'';
    $map=[
      'barang'=>['table'=>'barang','pk'=>'id_barang','fields'=>['kode_barang','nama_barang','kategori','satuan','stok','harga_beli','harga_jual','status_aktif']],
      'pelanggan'=>['table'=>'pelanggan','pk'=>'id_pelanggan','fields'=>['kode_pelanggan','nama_pelanggan','no_telepon','email','alamat']],
      'pemasok'=>['table'=>'pemasok','pk'=>'id_pemasok','fields'=>['kode_pemasok','nama_pemasok','no_telepon','email','alamat']],
      'karyawan'=>['table'=>'karyawan','pk'=>'id_karyawan','fields'=>['kode_karyawan','nama_karyawan','jabatan','no_telepon','alamat','status_aktif']],
    ];
    if(!isset($map[$type])) out(['error'=>'Unknown master data type'],400);
    $rows=$pdo->query("SELECT * FROM ".$map[$type]['table']." ORDER BY ".$map[$type]['pk']." ASC")->fetchAll();
    out(['data'=>$rows]);
  case 'save':
    $type=strv($input['type']??''); $id=(int)($input['id']??0);
    $map=[
      'barang'=>['table'=>'barang','pk'=>'id_barang','fields'=>['kode_barang','nama_barang','kategori','satuan','stok','harga_beli','harga_jual','status_aktif']],
      'pelanggan'=>['table'=>'pelanggan','pk'=>'id_pelanggan','fields'=>['kode_pelanggan','nama_pelanggan','no_telepon','email','alamat']],
      'pemasok'=>['table'=>'pemasok','pk'=>'id_pemasok','fields'=>['kode_pemasok','nama_pemasok','no_telepon','email','alamat']],
      'karyawan'=>['table'=>'karyawan','pk'=>'id_karyawan','fields'=>['kode_karyawan','nama_karyawan','jabatan','no_telepon','alamat','status_aktif']],
    ];
    if(!isset($map[$type])) out(['error'=>'Invalid data type'],400);
    $m=$map[$type]; $vals=[];
    foreach($m['fields'] as $f){$vals[$f]=$input[$f]??null;}
    $required=$m['fields'][0]; $nameField=$m['fields'][1];
    if(strv($vals[$required])===''||strv($vals[$nameField])==='') out(['error'=>'Required fields cannot be empty'],422);
    if($id){
      $set=implode(',',array_map(fn($f)=>"`$f`=:$f",$m['fields']));
      $stmt=$pdo->prepare("UPDATE {$m['table']} SET $set WHERE {$m['pk']}=:id");
      $vals['id']=$id; $stmt->execute($vals);
    } else {
      $cols=implode(',',array_map(fn($f)=>"`$f`",$m['fields']));
      $pars=implode(',',array_map(fn($f)=>":$f",$m['fields']));
      $stmt=$pdo->prepare("INSERT INTO {$m['table']} ($cols) VALUES ($pars)"); $stmt->execute($vals);
    }
    out(['success'=>true]);
  case 'delete':
    $type=strv($input['type']??''); $id=(int)($input['id']??0);
    $map=['barang'=>['barang','id_barang'],'pelanggan'=>['pelanggan','id_pelanggan'],'pemasok'=>['pemasok','id_pemasok'],'karyawan'=>['karyawan','id_karyawan'],'coa'=>['coa','kode_akun']];
    if(!isset($map[$type])) out(['error'=>'Invalid delete type'],400);
    if($type==='coa'){$stmt=$pdo->prepare("DELETE FROM coa WHERE kode_akun=?");$stmt->execute([strv($input['id']??'')]);}
    else {$stmt=$pdo->prepare("DELETE FROM {$map[$type][0]} WHERE {$map[$type][1]}=?");$stmt->execute([$id]);}
    out(['success'=>true]);
  case 'coa':
    out(['data'=>$pdo->query("SELECT * FROM coa ORDER BY kode_akun")->fetchAll()]);
  case 'save_coa':
    $code=strv($input['kode_akun']??''); $old=strv($input['old_kode_akun']??'');
    $name=strv($input['nama_akun']??''); $group=strv($input['kelompok_akun']??'');
    $type=strv($input['jenis_akun']??''); $normal=strv($input['saldo_normal']??'Debit');
    if(!$code||!$name||!$group||!$type) out(['error'=>'Please complete required COA fields'],422);
    if($old){
      $q=$pdo->prepare("UPDATE coa SET kode_akun=?,nama_akun=?,kelompok_akun=?,jenis_akun=?,saldo_normal=?,saldo_referensi=?,keterangan=?,status_aktif=? WHERE kode_akun=?");
      $q->execute([$code,$name,$group,$type,$normal,(float)($input['saldo_referensi']??0),strv($input['keterangan']??''),(int)($input['status_aktif']??1),$old]);
    } else {
      $q=$pdo->prepare("INSERT INTO coa(kode_akun,nama_akun,kelompok_akun,jenis_akun,saldo_normal,saldo_referensi,keterangan,status_aktif) VALUES(?,?,?,?,?,?,?,?)");
      $q->execute([$code,$name,$group,$type,$normal,(float)($input['saldo_referensi']??0),strv($input['keterangan']??''),(int)($input['status_aktif']??1)]);
    }
    out(['success'=>true]);
  case 'journals':
    $rows=$pdo->query("SELECT j.*, COALESCE(SUM(d.debit),0) total_debit, COALESCE(SUM(d.kredit),0) total_kredit FROM jurnal_umum j LEFT JOIN detail_jurnal d ON d.id_jurnal=j.id_jurnal GROUP BY j.id_jurnal ORDER BY j.tanggal ASC
    ,j.id_jurnal ASC")->fetchAll();
    out(['data'=>$rows]);
  case 'save_journal':
    $date=strv($input['tanggal']??''); $desc=strv($input['keterangan']??''); $rows=$input['details']??[];
    if(!$date||!$desc||count($rows)<2) out(['error'=>'Enter journal date, description, and at least two account lines'],422);
    $debit=0;$credit=0;
    foreach($rows as $r){$d=(float)($r['debit']??0);$c=(float)($r['kredit']??0); if(($d>0&&$c>0)||($d<=0&&$c<=0)) out(['error'=>'Each journal line must contain either debit or credit'],422);$debit+=$d;$credit+=$c;}
    if(round($debit,2)!==round($credit,2)) out(['error'=>'Journal is not balanced. Total debit must equal total credit.'],422);
    $pdo->beginTransaction();
    $number='JU-'.date('Ymd-His').'-'.random_int(10,99);
    $q=$pdo->prepare("INSERT INTO jurnal_umum(nomor_jurnal,tanggal,keterangan,status) VALUES(?,?,?,'Posted')");
    $q->execute([$number,$date,$desc]); $jid=(int)$pdo->lastInsertId();
    $d=$pdo->prepare("INSERT INTO detail_jurnal(id_jurnal,nomor_baris,kode_akun,keterangan,debit,kredit) VALUES(?,?,?,?,?,?)");
    foreach($rows as $i=>$r){$d->execute([$jid,$i+1,strv($r['kode_akun']??''),strv($r['keterangan']??''),(float)($r['debit']??0),(float)($r['kredit']??0)]);}
    $pdo->commit(); out(['success'=>true,'nomor_jurnal'=>$number]);
  case 'reports':
    $start=$_GET['start']??'2000-01-01'; $end=$_GET['end']??date('Y-m-d');
    $q=$pdo->prepare("SELECT c.kode_akun,c.nama_akun,c.kelompok_akun,c.saldo_normal,
      COALESCE(SUM(d.debit),0) debit,COALESCE(SUM(d.kredit),0) kredit
      FROM coa c
      LEFT JOIN jurnal_umum j ON j.status='Posted' AND j.tanggal BETWEEN ? AND ?
      LEFT JOIN detail_jurnal d ON d.id_jurnal=j.id_jurnal AND d.kode_akun=c.kode_akun
      GROUP BY c.kode_akun ORDER BY c.kode_akun");
    $q->execute([$start,$end]); $rows=$q->fetchAll();
    $tot=['Aset'=>0,'Liabilitas'=>0,'Ekuitas'=>0,'Pendapatan'=>0,'HPP'=>0,'Beban Operasional'=>0]; $trial=[];
    foreach($rows as $r){
      $net=(float)$r['debit']-(float)$r['kredit'];
      $balance=$r['saldo_normal']==='Debit'?$net:-$net;
      $trial[]=$r+['saldo'=>$balance];
      if(isset($tot[$r['kelompok_akun']])) $tot[$r['kelompok_akun']]+=$balance;
    }
    // When there are no posted journals in the selected period, display source workbook reference values.
    $countStmt=$pdo->prepare("SELECT COUNT(*) FROM jurnal_umum WHERE status='Posted' AND tanggal BETWEEN ? AND ?");
    $countStmt->execute([$start,$end]); $posted=(int)$countStmt->fetchColumn();
    $source=$posted===0;
    if($source){
      foreach($pdo->query("SELECT kelompok_akun,SUM(saldo_referensi) total FROM coa GROUP BY kelompok_akun")->fetchAll() as $g){$tot[$g['kelompok_akun']]=(float)$g['total'];}
    }
    out(['totals'=>$tot,'trial'=>$trial,'source_reference'=>$source,'posted_count'=>$posted]);
  case 'settings':
    out(['data'=>$pdo->query("SELECT * FROM profil_usaha ORDER BY id LIMIT 1")->fetch()]);
  case 'save_settings':
    $q=$pdo->prepare("UPDATE profil_usaha SET nama_usaha=?,jenis_usaha=?,alamat=?,telepon=?,standar_akuntansi=? WHERE id=1");
    $q->execute([strv($input['nama_usaha']??''),strv($input['jenis_usaha']??''),strv($input['alamat']??''),strv($input['telepon']??''),strv($input['standar_akuntansi']??'SAK EMKM')]);
    out(['success'=>true]);
  default: out(['error'=>'Unknown action'],404);
 }
} catch (Throwable $e) {
 if($pdo->inTransaction()) $pdo->rollBack();
 out(['error'=>$e->getMessage()],500);
}
