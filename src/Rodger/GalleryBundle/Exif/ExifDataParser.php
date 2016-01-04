<?php
namespace Rodger\GalleryBundle\Exif;

class ExifDataParser
{
    protected $data, $parsed = array(), $look_for;
    protected static $flash_types = array(
        "0"  => "Flash did not fire.",
        "1"  => "Flash fired.",
        "5"  => "Strobe return light not detected.",
        "7"  => "Strobe return light detected.",
        "9"  => "Flash fired, compulsory flash mode",
        "13" => "Flash fired, compulsory flash mode, return light not detected",
        "15" => "Flash fired, compulsory flash mode, return light detected",
        "16" => "Flash did not fire, compulsory flash mode",
        "24" => "Flash did not fire, auto mode",
        "25" => "Flash fired, auto mode",
        "29" => "Flash fired, auto mode, return light not detected",
        "31" => "Flash fired, auto mode, return light detected",
        "32" => "No flash function",
        "65" => "Flash fired, red-eye reduction mode",
        "69" => "Flash fired, red-eye reduction mode, return light not detected",
        "71" => "Flash fired, red-eye reduction mode, return light detected",
        "73" => "Flash fired, compulsory flash mode, red-eye reduction mode",
        "77" => "Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected",
        "79" => "Flash fired, compulsory flash mode, red-eye reduction mode, return light detected",
        "89" => "Flash fired, auto mode, red-eye reduction mode",
        "93" => "Flash fired, auto mode, return light not detected, red-eye reduction mode",
        "95" => "Flash fired, auto mode, return light detected, red-eye reduction mode"
    );

    /**
     * ExifDataParser constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->data     = $data;
        $this->look_for = array(
            'IFD0' => array('Make', 'Model', 'Orientation'),
            'EXIF' => array(
                'ISOSpeedRatings',
                'Flash',
                'ExposureTime',
                "FNumber",
                "ExposureProgram",
                "DateTime",
                "DateTimeOriginal",
                "ShutterSpeedValue",
                "ApertureValue",
                "ExposureBiasValue",
                "MaxApertureValue",
                "MeteringMode",
                "FocalLength",
            )
        );
    }

    public function getParsed()
    {
        $this->prepare_ifd0();
        $this->prepare_exif();

        return $this->parsed;
    }

    protected function prepare_ifd0()
    {
        $keys       = $this->look_for['IFD0'];
        $prepared   = array();
        $found_keys = array_intersect($keys, array_keys(@$this->data['IFD0'] ?: array()));
        foreach ($found_keys as $key) {
            $prepared[$key] = trim($this->data['IFD0'][$key]);
        }

        if (@$prepared['Make'] || @$prepared['Model']) {
            $prepared['Camera'] = implode(' ', array_filter(array(@$prepared['Make'], @$prepared['Model'])));
            unset($prepared['Make'], $prepared['Model']);
        }

        $this->parsed = $this->parsed + $prepared;
    }

    protected function prepare_exif()
    {
        $keys       = $this->look_for['EXIF'];
        $prepared   = array();
        $found_keys = array_intersect($keys, array_keys(@$this->data ?: array()));

        foreach ($found_keys as $key) {
            $prepared[$key] = $this->data[$key];
        }

        if (array_key_exists('Flash', $prepared) && @self::$flash_types[$prepared['Flash']]) {
            $prepared['Flash'] = self::$flash_types[$prepared['Flash']];
        }
        if (array_key_exists('ExposureTime', $prepared) && preg_match(
                '/^(\d{2,})\/(\d+)$/',
                $prepared['ExposureTime'],
                $matches
            )
        ) {
            $value                    = $matches[1] / $matches[2];
            $prepared['ExposureTime'] = $value >= 1 ? sprintf("%ss", $value) : sprintf("1/%ss", floor(1 / $value));
        }

        if (@$prepared['DateTimeOriginal']) {
            try {
                $d = new \DateTime($prepared['DateTimeOriginal']);
            } catch (\Exception $e) {
                unset($prepared['DateTimeOriginal']);
            }
        }

        $this->parsed = $this->parsed + $prepared;
    }


}

?>
