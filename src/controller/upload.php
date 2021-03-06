<?php
include_once "../connect.php";
include_once "../dao/tagDao.php";
include_once "../dao/articleDao.php";

define('TARGET', '../media/');
define('MAX_SIZE', 100000000);
 
$tabExt = array('jpg','gif','png','jpeg');
$infosImg = array();
 
$extension = '';
$message = null;
$nomImage = '';
 
if( !is_dir(TARGET) ) {
  if( !mkdir(TARGET, 0755) ) {
    exit('Erreur : le répertoire cible ne peut-être créé ! Vérifiez que vous diposiez des droits suffisants pour le faire ou créez le manuellement !');
  }
}
 
if(!empty($_POST))
{
  if( !empty($_FILES['fichier']['name']) )
  {
    $extension  = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
 
    if(in_array(strtolower($extension),$tabExt))
    {
      $infosImg = getimagesize($_FILES['fichier']['tmp_name']);
 
      if($infosImg[2] >= 1 && $infosImg[2] <= 14)
      {
        if(isset($_FILES['fichier']['error']) 
          && UPLOAD_ERR_OK === $_FILES['fichier']['error'])
        {
          $nomImage = md5(uniqid()) .'.'. $extension;

          if(move_uploaded_file($_FILES['fichier']['tmp_name'], TARGET.$nomImage))
          {
            $link = substr(TARGET, 2).$nomImage;
            $vars = array();
            $vars['media'] = $link;
            $vars['title'] = htmlentities($_POST['title']);
            $vars['content'] = htmlentities($_POST['content']);
            $vars['user_id'] = $_SESSION['id'];
            $article = saveNewArticle($vars);
            foreach($_POST as $key => $value) {
              if (strpos($key, 'tag') === 0) {
                $tag = explode('_', $key);
                $tag_id = $tag[1];
                $tag = saveNewTag($article, $tag_id);
              }
            }
            header("Location: /?article&id=".$article);
          }
          else
          {
            $message = 'Problème lors de l\'upload !';
          }
        }
        else
        {
        $message = 'Une erreur interne a empêché l\'uplaod de l\'image';
        }
      }
      else
      {
        $message = 'Le fichier à uploader n\'est pas une image !';
      }
    }
    else
    {
      $message = 'L\'extension du fichier est incorrecte !';
    }
  }
  else
  {
    $message = 'Veuillez remplir le formulaire svp !';
  }
  if(isset($message)){
    header("Location: /?new&err_msg=".$message);
  }
}