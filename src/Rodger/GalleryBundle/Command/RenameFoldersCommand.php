<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodger
 * Date: 26.03.13
 * Time: 0:12
 * To change this template use File | Settings | File Templates.
 */

namespace Rodger\GalleryBundle\Command;

use Rodger\GalleryBundle\Entity\Album;
use Rodger\GalleryBundle\Entity\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;


class RenameFoldersCommand extends ContainerAwareCommand
{

  protected $result = array();

  protected function configure()
  {
    $this
      ->setName('gallery:rename-folders')
      ->setDescription('Renames folders, slug => id');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    /** @var AlbumRepository $repo */
    $repo    = $this->getContainer()->get('doctrine')->getRepository('RodgerGalleryBundle:Album');
    $webroot = $this->getContainer()->getParameter('web_root');

    $result = array();

    foreach ($repo->findAll() as $album) {
      /** @var Album $album */
      $output->writeln(sprintf("<info>[%s]</info>\t%s => %s", $album->getName(), $album->getSlug(), $album->getId()));
      $finder = new Finder();
      foreach ($finder->directories()->name($album->getSlug())->in($webroot) as $dir) {
        $output->writeln($dir->getRealpath());
        $this->addResult($album, $dir);
      }

      $finder = new Finder();
      foreach ($finder->directories()->name($album->getSlug())->in($this->getContainer()->getParameter('kernel.root_dir') . '/../uploads') as $dir) {
        $output->writeln($dir->getRealpath());
        $this->addResult($album, $dir);
      }
    }

    foreach ($this->result as $arr) {
      $output->writeln(sprintf("<info>%s => %s</info>", $arr['old'], $arr['new']));
      exec(sprintf("mv %s %s", $arr['old'], $arr['new']));
    }
  }

  protected function addResult(Album $album, $dir)
  {

    $new_dirname = preg_replace("#\/" . $album->getSlug() . "$#", "/" . $album->getId(), $dir->getRealpath());

    $this->result[] = array('old' => $dir->getRealpath(), 'new' => $new_dirname);
  }
}