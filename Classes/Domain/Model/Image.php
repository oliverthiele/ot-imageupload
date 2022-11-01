<?php

declare(strict_types=1);

namespace OliverThiele\OtImageupload\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Annotation\Validate;

/**
 * Image
 */
class Image extends AbstractEntity
{
    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected string $name = '';

    /**
     * @var int
     */
    protected int $size = 0;

    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var string
     */
    protected string $tmpName = '';

    /**
     * @var int
     */
    protected int $error = 0;

    /**
     * @var int
     */
    protected int $width = 0;

    /**
     * @var int
     */
    protected int $height = 0;

    /**
     * @var string
     */
    protected string $imageType = '';

    /**
     * @var int
     */
    protected int $bits = 0;

    /**
     * @param  array  $image
     */
    public function __construct(array $image)
    {
        if (isset($image['name'])) {
            $this->setName($image['name']);
        }
        if (isset($image['size'])) {
            $this->setSize($image['size']);
        }
        if (isset($image['type'])) {
            $this->setType($image['type']);
        }
        if (isset($image['tmp_name'])) {
            $this->setTmpName($image['tmp_name']);
        }
        if (isset($image['error'])) {
            $this->setError($image['error']);
        }
        $this->setImageProperties();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param  int  $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param  string  $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    /**
     * @param  string  $tmpName
     */
    public function setTmpName(string $tmpName): void
    {
        $this->tmpName = $tmpName;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @param  int  $error
     */
    public function setError(int $error): void
    {
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param  int  $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param  int  $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getImageType(): string
    {
        return $this->imageType;
    }

    /**
     * @param  string  $imageType
     */
    public function setImageType(string $imageType): void
    {
        $this->imageType = $imageType;
    }

    /**
     * @return int
     */
    public function getBits(): int
    {
        return $this->bits;
    }

    /**
     * @param  int  $bits
     */
    public function setBits(int $bits): void
    {
        $this->bits = $bits;
    }


    private function setImageProperties(): void
    {
        $imageTypeArray = [
            0 => 'UNKNOWN',
            1 => 'GIF',
            2 => 'JPEG',
            3 => 'PNG',
            4 => 'SWF',
            5 => 'PSD',
            6 => 'BMP',
            7 => 'TIFF_II',
            8 => 'TIFF_MM',
            9 => 'JPC',
            10 => 'JP2',
            11 => 'JPX',
            12 => 'JB2',
            13 => 'SWC',
            14 => 'IFF',
            15 => 'WBMP',
            16 => 'XBM',
            17 => 'ICO',
            18 => 'COUNT'
        ];

        if (is_file($this->getTmpName())) {
            $imageSizes = getimagesize($this->getTmpName());

            if (isset($imageSizes[0])) {
                $this->setWidth($imageSizes[0]);
            }
            if (isset($imageSizes[1])) {
                $this->setHeight($imageSizes[1]);
            }
            if (isset($imageSizes[2])) {
                $this->setImageType($imageTypeArray[$imageSizes[2]]);
            }
            if (isset($imageSizes['bits'])) {
                $this->setBits($imageSizes['bits']);
            }
        }
    }


}
