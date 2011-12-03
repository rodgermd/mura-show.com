<?php 

 // connect to symfony
require_once __DIR__.'/../../app/bootstrap.php.cache';
require_once __DIR__.'/../../app/AppKernel.php';

use Rodger\ImageSizeBundle\Entity\ImageSize;
use Rodger\GalleryBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$kernel->handle(Request::createFromGlobals());


// this directory
$thisDir = dirname(__FILE__).'/';

// die if no image is set
if (!$img = $_GET['img'])
  die('no img');
  
// make the filename safe to use
//$img = strip_tags(htmlspecialchars($img));
$img = trim($img);
$img = str_replace('..', '', $img); // no going up in directory tree

// get the command specified in the filename
// example: big_green_frog.thumb150.jpg
preg_match("/\.([^\.]*)\..{3}$/", $img, $match); // this also means no double commands (possible DOS attack)
//var_dump($match); die();
if (!$command = @$match[1]) die('no command');

// get original filename
// example: big_green_frog.jpg
$imgFile = str_replace('.'.$command, '', $img, $replaceCount);

$folder = pathinfo($imgFile, PATHINFO_DIRNAME);

if (!file_exists(__DIR__ . '/' . $folder)) {
  mkdir(__DIR__ . '/' . $folder, 0777);
  chmod(__DIR__ . '/' . $folder, 0777);
} 

// stop the possibility of creating unlimited files
// example (attack): abc.120.jpg, abc.120.120.jpg, abc.120.....120.jpg - this could go on forever
if ($replaceCount > 1)
  die('command specified more than 1 time');

$em = $kernel->getContainer()->get('doctrine');

$object = null;

try {
  $object = $em->getRepository('ImageBankGalleryBundle:Image')->findOneBy(array('path' => $imgFile));
  if (!$object) throw new ImageNotFoundException();
}
catch (ImageNotFoundException $e) 
{
  $object = $em->getRepository('ImageBankPagesBundle:Page')->findOneBy(array('image_path' => $imgFile));
}
if (!$object) die('nothing found using requested file');
$original_dir = $object->getUploadRootDir() . '/';

// die if the original file does not exist
if (!file_exists($original_dir.$imgFile))
  die('original file does not exist');

$filename = $imgFile;

############################################################################

// find image size template
$doctrine = $kernel->getContainer()->get('doctrine');
$image_size = $doctrine->getRepository('ImageBankImageSizeBundle:ImageSize')->findOneByName($command);
if (!$image_size) die(sprintf('Template %s not found', $command));
if ($image_size->isPrivate()) die('Restricted thumbnail template');

$converter = new Converter($original_dir . $imgFile, $thisDir . $img, $image_size);
$converter->convert();

// redirect the browser to the new image - this is more reliable than fpassthru
header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.$img);
exit;
?>
