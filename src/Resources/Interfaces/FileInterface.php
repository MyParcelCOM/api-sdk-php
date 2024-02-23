<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use Psr\Http\Message\StreamInterface;

interface FileInterface extends ResourceInterface
{
    const DOCUMENT_TYPE_LABEL = 'label';
    const DOCUMENT_TYPE_PRINTCODE = 'printcode';
    const DOCUMENT_TYPE_CUSTOMS_DECLARATION_FORM = 'customs-declaration-form';
    const DOCUMENT_TYPE_COMMERCIAL_INVOICE = 'commercial-invoice';
    const EXTENSION_PDF = 'pdf';

    const MIME_TYPE_JSON = 'application/vnd.api+json';
    const MIME_TYPE_PNG = 'image/png';
    const MIME_TYPE_JPG = 'image/jpeg';
    const MIME_TYPE_PDF = 'application/pdf';
    const MIME_TYPE_ZPL = 'application/zpl';

    const FORMAT_MIME_TYPE = 'mime_type';
    const FORMAT_EXTENSION = 'extension';

    /**
     * Set the type of file. See constants for possible options.
     */
    public function setDocumentType(string $documentType): self;

    /**
     * Get the type of file. See constants for possible options.
     */
    public function getDocumentType(): string;

    /**
     * Set the formats that this file has available.
     */
    public function setFormats(array $formats): self;

    /**
     * Add a format this file is available as.
     */
    public function addFormat(string $mimeType, string $extension): self;

    /**
     * Get an array of the available formats for this file. Each format is an
     * associative array with keys 'mime_type' and 'extension'.
     */
    public function getFormats(): array;

    /**
     * Set the stream of this file for given mime type.
     */
    public function setStream(StreamInterface $stream, string $mimeType): self;

    /**
     * Get a stream for this file. If no mime type supplied one is chosen in
     * the following order from the available formats:
     * application/pdf > image/png > image/jpeg > other
     */
    public function getStream(?string $mimeType = null): ?StreamInterface;

    /**
     * Set the base64 encoded data of this file for given mime type.
     */
    public function setBase64Data(string $data, string $mimeType): self;

    /**
     * Get the file data as a base64 encoded string. If no mime type supplied
     * one is chosen in the following order from the available formats:
     * application/pdf > image/png > image/jpeg > other
     */
    public function getBase64Data(?string $mimeType = null): ?string;

    /**
     * Set the path where this file can be found for given mime type.
     */
    public function setTemporaryFilePath(string $path, string $mimeType): self;

    /**
     * Get a path for this file saved in a temporary location on the filesystem.
     * If no mime type is supplied one is chosen in the following order from the
     * available formats:
     * application/pdf > image/png > image/jpeg > other
     */
    public function getTemporaryFilePath(?string $mimeType = null): ?string;
}
