<?php
namespace Rodger\GalleryBundle\Exif;

class IptcDataParser
{
  protected $iptc, $iptc_app13;
  public function __construct($file)
  {
    if (!(file_exists($file) && is_readable($file))) throw new FileReadException(sprintf('file %s in not readable', $file));
    getimagesize( $file, $this->iptc );
    $this->iptc_app13 = @iptcparse($this->iptc['APP13']);
  }
  
  /**
   * Gets title
   * @return string
   */
  public function getTitle() {
    return (string) @$this->iptc_app13['2#005'][0];
  }
  
  /**
   * Gets description
   * @return string
   */
  public function getDescription() {
    return (string) @$this->iptc_app13['2#120'][0];
  }
  
  /**
   * gets array of keywords
   * @return array
   */
  public function getKeywords() {
    return @$this->iptc_app13['2#025'] ?: array();
  }
}
?>
