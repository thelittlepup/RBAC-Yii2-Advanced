<?php
namespace console\controllers;

use Yii;
use yii\helpers\Console;


class RbacController extends \yii\console\Controller {

  public function actionInit(){
      
    $auth = Yii::$app->authManager;
    $auth->removeAll();
    Console::output('Removing All! RBAC.....');
    
    $createPost = $auth->createPermission('createBlog');
    $createPost->description = 'สำหรับการสร้างบทความ';
    $auth->add($createPost);

    $updatePost = $auth->createPermission('updateBlog');
    $updatePost->description = 'สำหรับการอัพเดตบทความ';
    $auth->add($updatePost);
    
    // เพิ่ม permission loginToBackend <<<------------------------
      $loginToBackend = $auth->createPermission('loginToBackend');
      $loginToBackend->description = 'ล็อกอินเข้าใช้งานส่วน backend';
      $auth->add($loginToBackend);
    
    $admin = $auth->createRole('Admin');
    $admin->description = 'สำหรับการดูแลระบบ';
    $auth->add($admin);

    $author = $auth->createRole('Author');
    $author->description = 'สำหรับการเขียนบทความ';
    $auth->add($author);

    $management = $auth->createRole('Management');
    $management->description = 'สำหรับจัดการข้อมูลผู้ใช้งานและบทความ';
    $auth->add($management);
    
    // เรียกใช้งาน AuthorRule
    $rule = new \common\rbac\AuthorRule;
    $auth->add($rule);
    
    // สร้าง permission ขึ้นมาใหม่เพื่อเอาไว้ตรวจสอบและนำ AuthorRule มาใช้งานกับ updateOwnPost
    $updateOwnPost = $auth->createPermission('updateOwnPost');
    $updateOwnPost->description = 'สำหรับการอัพเดตบทความของตัวเอง';
    $updateOwnPost->ruleName = $rule->name;
    $auth->add($updateOwnPost);
    
    $auth->addChild($author,$createPost); //การสร้างบทความอยู่ภายใต้ $author
    
    // เปลี่ยนลำดับ โดยใช้ updatePost อยู่ภายใต้ updateOwnPost และ updateOwnPost อยู่ภายใต้ author อีกที
    $auth->addChild($updateOwnPost, $updatePost); // ผู้ที่อัพเดตต้องเป็นเจ้าของโพส
    $auth->addChild($author, $updateOwnPost); // ผู้ที่เป็นเจ้าของโพส อยู่ใต้ $author
    
    // addChild role ManageUser <<<------------------------
    $auth->addChild($manageUser, $loginToBackend);
    
    $auth->addChild($management, $author); // $author อยู่ภายใต้ $management
    $auth->addChild($management, $manageUser); // $manageUser อยู่ภายใต้ $management
    
    $auth->addChild($admin, $management); // $management อยู่ภายใต้ $admin

    $auth->assign($admin, 1);
    $auth->assign($management, 2);
    $auth->assign($author, 3);
    $auth->assign($author, 4);
    
    Console::output('Success! RBAC roles has been added.');
  }

}
?>