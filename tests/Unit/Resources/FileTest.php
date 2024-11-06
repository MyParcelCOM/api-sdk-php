<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\File;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class FileTest extends TestCase
{
    /** @var array */
    private $pngFormat;
    /** @var array */
    private $jpegFormat;
    /** @var array */
    private $pdfFormat;
    /** @var StreamInterface */
    private $stream;
    /** @var string */
    private $path;
    /** @var string */
    private $streamTestData;
    /** @var string */
    private $base64TestData;
    /** @var string */
    private $pathTestData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pngFormat = [
            'mime_type' => 'image/png',
            'extension' => 'png',
        ];

        $this->jpegFormat = [
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
        ];

        $this->pdfFormat = [
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
        ];

        $this->streamTestData = 'Some stream test data.';
        $this->stream = $this->createMock(StreamInterface::class);
        $this->stream
            ->method('getContents')
            ->willReturn($this->streamTestData);

        $this->base64TestData = base64_encode('Some base64 test data.');

        $this->pathTestData = 'Some path test data.';
        $this->path = tempnam(sys_get_temp_dir(), 'myparcelcom_file') . '.pdf';
        file_put_contents($this->path, $this->pathTestData);
    }

    /** @test */
    public function testId()
    {
        $file = new File();

        $this->assertNull($file->getId());
        $this->assertEquals('4bquri', $file->setId('4bquri')->getId());
    }

    /** @test */
    public function testType()
    {
        $file = new File();

        $this->assertEquals('files', $file->getType());
    }

    /** @test */
    public function testDocumentType()
    {
        $file = new File();

        $this->assertEquals('some-resource-type', $file->setDocumentType('some-resource-type')->getDocumentType());
    }

    /** @test */
    public function testFormats()
    {
        $file = new File();

        $this->assertEmpty($file->getFormats());
        $this->assertEquals([$this->jpegFormat], $file->addFormat($this->jpegFormat['mime_type'], $this->jpegFormat['extension'])->getFormats());

        // Setting an array of formats removes all other formats.
        $this->assertEquals(
            [$this->pdfFormat, $this->pngFormat],
            $file
                ->addFormat($this->jpegFormat['mime_type'], $this->jpegFormat['extension'])
                ->setFormats([$this->pdfFormat, $this->pngFormat])
                ->getFormats()
        );

        // Adding a format to an array of existing formats keeps the old ones.
        // Order of the formats is always pdf->png->jpeg.
        $this->assertEquals(
            [$this->pdfFormat, $this->pngFormat, $this->jpegFormat],
            $file
                ->setFormats([$this->pngFormat, $this->jpegFormat])
                ->addFormat($this->pdfFormat['mime_type'], $this->pdfFormat['extension'])
                ->getFormats()
        );
    }

    /** @test */
    public function testStream()
    {
        $file = new File();

        $this->assertNull($file->getStream());

        $stream_A = $file
            ->setStream($this->stream, 'image/png')
            ->getStream('image/png');
        $this->assertInstanceOf(StreamInterface::class, $stream_A);
        $this->assertEquals($this->streamTestData, $stream_A->getContents());

        // Using getStream() without giving a mime_type loops over the
        // file's formats and returns the first stream it encounters.
        $stream_B = $file
            ->addFormat($this->pngFormat['mime_type'], $this->pngFormat['extension'])
            ->getStream();
        $this->assertInstanceOf(StreamInterface::class, $stream_B);
        $this->assertEquals($this->streamTestData, $stream_B->getContents());

        // If no stream is set under the given mime_type,
        // getStream() will look for data in other sources
        // (base64Data or temporary files).
        $newFile = new File();

        $base64Stream = $newFile
            ->setBase64Data($this->base64TestData, 'image/jpeg')
            ->getStream('image/jpeg');
        $this->assertInstanceOf(StreamInterface::class, $base64Stream);
        $this->assertEquals(base64_decode($this->base64TestData), $base64Stream->getContents());

        $fileStream = $newFile
            ->setTemporaryFilePath($this->path, 'application/pdf')
            ->getStream('application/pdf');
        $this->assertInstanceOf(StreamInterface::class, $fileStream);
        $this->assertEquals($this->pathTestData, $fileStream->getContents());

        // Requesting a stream for a mime_type that has not been set
        // should return null.
        $this->assertNull($newFile->getStream('image/png'));
    }

    /** @test */
    public function testBase64Data()
    {
        $file = new File();

        $this->assertNull($file->getBase64Data());

        $this->assertEquals(
            $this->base64TestData,
            $file
                ->setBase64Data($this->base64TestData, 'application/pdf')
                ->getBase64Data('application/pdf')
        );

        // Using getBase64Data() without giving a mime_type loops over the
        // file's formats and returns the first base64Data it encounters.
        $this->assertEquals(
            $this->base64TestData,
            $file
                ->addFormat($this->pdfFormat['mime_type'], $this->pdfFormat['extension'])
                ->getBase64Data()
        );

        // If no base64Data is set under the given mime_type,
        // getBase64Data will look for data in other sources
        // (streams or temporary files).
        $newFile = new File();

        $this->assertEquals(
            base64_encode($this->streamTestData),
            $newFile
                ->setStream($this->stream, 'image/jpeg')
                ->getBase64Data('image/jpeg')
        );
        $this->assertEquals(
            base64_encode($this->pathTestData),
            $newFile
                ->setTemporaryFilePath($this->path, 'application/pdf')
                ->getBase64Data('application/pdf')
        );

        // Requesting base64Data for a mime_type that has not been set
        // should return null.
        $this->assertNull($newFile->getBase64Data('image/png'));
    }

    /** @test */
    public function testTemporaryFilePath()
    {
        $file = new File();

        $this->assertNull($file->getTemporaryFilePath());

        $filePath_A = $file
            ->setTemporaryFilePath($this->path, 'application/pdf')
            ->getTemporaryFilePath('application/pdf');
        $this->assertEquals($this->pathTestData, file_get_contents($filePath_A));

        // Using $this->getTemporaryFilePath() without giving a mime_type loops over the
        // file's formats and returns the first path it encounters.
        $filePath_B = $file
            ->addFormat($this->pdfFormat['mime_type'], $this->pdfFormat['extension'])
            ->getTemporaryFilePath();
        $this->assertEquals($this->pathTestData, file_get_contents($filePath_B));
        // If no path is set under the given mime_type,
        // $this->getTemporaryFilePath() will look for data in other sources
        // (streams or base64Data).
        $streamFile = new File();

        $streamPath = $streamFile
            ->addFormat($this->pdfFormat['mime_type'], $this->pdfFormat['extension'])
            ->setStream($this->stream, 'application/pdf')
            ->getTemporaryFilePath('application/pdf');
        $this->assertEquals($this->streamTestData, file_get_contents($streamPath));

        $base64File = new File();

        $base64Path = $base64File
            ->addFormat($this->pdfFormat['mime_type'], $this->pdfFormat['extension'])
            ->setBase64Data($this->base64TestData, 'application/pdf')
            ->getTemporaryFilePath('application/pdf');
        $this->assertEquals(base64_decode($this->base64TestData), file_get_contents($base64Path));

        // Requesting a path for a mime_type that has not been set
        // should return null.
        $this->assertNull($file->getTemporaryFilePath('image/jpeg'));
    }
}
