<?php
session_start();
require '_base.php';

header('Content-Type: application/json');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_SESSION['user'])) {
  die(json_encode(['success' => false, 'message' => '请先登录']));
}


if (!isset($_FILES['avatar'])) {
  die(json_encode(['success' => false, 'message' => '未选择文件']));
}

$uploadDir = 'uploads/avatars/';
$allowedTypes = ['image/jpeg', 'image/png'];
$maxSize = 2 * 1024 * 1024; // 2MB


if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}


try {
  $file = $_FILES['avatar'];
  

  if (!in_array($file['type'], $allowedTypes)) {
    throw new Exception('文件类型不支持');
  }

  if ($file['size'] > $maxSize) {
    throw new Exception('文件大小超过限制');
  }


  $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
  $filename = 'avatar_' . $_SESSION['user']['id'] . '_' . md5_file($file['tmp_name']) . '.' . $extension;
  $targetPath = $uploadDir . $filename;

  
  if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    throw new Exception('文件保存失败');
  }

  // 修改查询和更新部分
$stmt = $_db->prepare("UPDATE customer SET profile_pic = ? WHERE user_id = ?");
$stmt->execute([$targetPath, $_SESSION['user_id']]);

// 更新session
$_SESSION['user']['profile_pic'] = $targetPath;

  echo json_encode([
    'success' => true,
    'url' => $targetPath,
    'message' => '上传成功'
  ]);

} catch (Exception $e) {
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
}