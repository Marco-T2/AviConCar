<?php
header('Content-Type: application/json; charset=utf-8');
try{ include('../../config.php'); }catch(Exception $e){ echo json_encode(['ok'=>false,'error'=>'config']); exit; }
session_start();
if(empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')){ echo json_encode(['ok'=>false,'error'=>'csrf']); exit; }
$persona = isset($_POST['persona_id']) ? (int)$_POST['persona_id'] : 0;
$tag = isset($_POST['tag']) ? trim($_POST['tag']) : '';
$action = isset($_POST['action']) ? $_POST['action'] : 'toggle';
if(!$persona || $tag === ''){ echo json_encode(['ok'=>false,'error'=>'missing']); exit; }
try{
  // find or create tag
  $st = $pdo->prepare("SELECT id FROM tb_tags WHERE tag = ? LIMIT 1");
  $st->execute([$tag]);
  $r = $st->fetch(PDO::FETCH_ASSOC);
  if($r) $tag_id = (int)$r['id']; else { $ins = $pdo->prepare("INSERT INTO tb_tags (tag, descripcion, created_at) VALUES (?, ?, NOW())"); $ins->execute([$tag, null]); $tag_id = (int)$pdo->lastInsertId(); }

  if($action === 'on' || ($action==='toggle' && !empty($_POST['state']) && $_POST['state']=='1')){
    $ins2 = $pdo->prepare("INSERT IGNORE INTO tb_persona_tags (persona_id, tag_id, created_at) VALUES (?, ?, NOW())");
    $ins2->execute([$persona, $tag_id]);
    echo json_encode(['ok'=>true,'action'=>'on']);
    exit;
  }
  // remove
  $del = $pdo->prepare("DELETE FROM tb_persona_tags WHERE persona_id = ? AND tag_id = ?");
  $del->execute([$persona, $tag_id]);
  echo json_encode(['ok'=>true,'action'=>'off']);
  exit;
}catch(Exception $e){ echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); exit; }

?>
