<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use GuzzleHttp\Psr7\Utils;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;
use Psr\Http\Message\StreamInterface;

class File implements FileInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_DOCUMENT_TYPE = 'document_type';
    const ATTRIBUTE_FORMATS = 'formats';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_FILE;

    private array $attributes = [
        self::ATTRIBUTE_FORMATS       => [],
        self::ATTRIBUTE_DOCUMENT_TYPE => null,
    ];

    /** @var StreamInterface[] */
    private array $streams = [];

    /** @var string[] */
    private array $base64Data = [];

    /** @var string[] */
    private array $paths = [];

    public function setDocumentType(string $documentType): self
    {
        $this->attributes[self::ATTRIBUTE_DOCUMENT_TYPE] = $documentType;

        return $this;
    }

    public function getDocumentType(): string
    {
        return $this->attributes[self::ATTRIBUTE_DOCUMENT_TYPE];
    }

    public function setFormats(array $formats): self
    {
        $this->attributes[self::ATTRIBUTE_FORMATS] = [];
        array_walk($formats, function ($format) {
            $this->addFormat($format[self::FORMAT_MIME_TYPE], $format[self::FORMAT_EXTENSION]);
        });

        return $this;
    }

    public function addFormat(string $mimeType, string $extension): self
    {
        $this->attributes[self::ATTRIBUTE_FORMATS][] = [
            self::FORMAT_MIME_TYPE => $mimeType,
            self::FORMAT_EXTENSION => $extension,
        ];

        usort($this->attributes[self::ATTRIBUTE_FORMATS], function ($formatA, $formatB) {
            $mimeTypeOrder = [self::MIME_TYPE_PDF => -3, self::MIME_TYPE_PNG => -2, self::MIME_TYPE_JPG => -1];

            return $mimeTypeOrder[$formatA[self::FORMAT_MIME_TYPE]] - $mimeTypeOrder[$formatB[self::FORMAT_MIME_TYPE]];
        });

        return $this;
    }

    public function getFormats(): array
    {
        return $this->attributes[self::ATTRIBUTE_FORMATS];
    }

    public function setStream(StreamInterface $stream, string $mimeType): self
    {
        $this->streams[$mimeType] = $stream;

        return $this;
    }

    public function getStream(?string $mimeType = null): ?StreamInterface
    {
        if ($mimeType === null) {
            foreach ($this->getFormats() as $format) {
                $stream = $this->getStream($format[self::FORMAT_MIME_TYPE]);

                if ($stream !== null) {
                    return $stream;
                }
            }

            return null;
        }

        if (isset($this->streams[$mimeType])) {
            return $this->streams[$mimeType];
        }
        if (isset($this->base64Data[$mimeType])) {
            return Utils::streamFor(base64_decode($this->base64Data[$mimeType]));
        }
        if (isset($this->paths[$mimeType])) {
            return Utils::streamFor(fopen($this->paths[$mimeType], 'r'));
        }

        return null;
    }

    public function setBase64DataFromResponseMeta(array $metaFiles): self
    {
        $format = $this->getFormats()[0];

        foreach ($metaFiles as $metaFile) {
            if ($metaFile['document_type'] === $this->getDocumentType()
                && $metaFile['mime_type'] === $format[FileInterface::FORMAT_MIME_TYPE]
                && $metaFile['extension'] === $format[FileInterface::FORMAT_EXTENSION]
            ) {
                $this->setBase64Data($metaFile['contents'], $metaFile['mime_type']);
            }
        }

        return $this;
    }

    public function setBase64Data(string $data, string $mimeType): self
    {
        $this->base64Data[$mimeType] = $data;

        return $this;
    }

    public function getBase64Data(?string $mimeType = null): ?string
    {
        if ($mimeType === null) {
            foreach ($this->getFormats() as $format) {
                $data = $this->getBase64Data($format[self::FORMAT_MIME_TYPE]);

                if ($data !== null) {
                    return $data;
                }
            }

            return null;
        }

        if (isset($this->base64Data[$mimeType])) {
            return $this->base64Data[$mimeType];
        }
        if (isset($this->paths[$mimeType])) {
            return $this->base64Data[$mimeType] = base64_encode(file_get_contents($this->paths[$mimeType]));
        }
        if (isset($this->streams[$mimeType])) {
            // Rewind the stream to ensure that we're getting all the contents.
            $this->streams[$mimeType]->rewind();

            return $this->base64Data[$mimeType] = base64_encode($this->streams[$mimeType]->getContents());
        }

        return null;
    }

    public function setTemporaryFilePath(string $path, string $mimeType): self
    {
        $this->paths[$mimeType] = $path;

        return $this;
    }

    public function getTemporaryFilePath(?string $mimeType = null): ?string
    {
        $extension = null;
        foreach ($this->getFormats() as $format) {
            if ($mimeType === null || $mimeType === $format[self::FORMAT_MIME_TYPE]) {
                $mimeType = $format[self::FORMAT_MIME_TYPE];
                $extension = $format[self::FORMAT_EXTENSION];
                break;
            }
        }

        if (isset($this->paths[$mimeType])) {
            return $this->paths[$mimeType];
        }
        if (isset($this->base64Data[$mimeType])) {
            $path = tempnam(sys_get_temp_dir(), 'myparcelcom_file') . '.' . $extension;
            file_put_contents($path, base64_decode($this->base64Data[$mimeType]));

            return $this->paths[$mimeType] = $path;
        }
        if (isset($this->streams[$mimeType])) {
            $path = tempnam(sys_get_temp_dir(), 'myparcelcom_file') . '.' . $extension;
            file_put_contents($path, $this->streams[$mimeType]->getContents());

            return $this->paths[$mimeType] = $path;
        }

        return null;
    }

    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['streams']);
        unset($values['base64Data']);
        unset($values['paths']);

        $json = $this->arrayValuesToArray($values);

        if (isset($json['attributes']) && $this->isEmpty($json['attributes'])) {
            unset($json['attributes']);
        }

        return $json;
    }
}
