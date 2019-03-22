<?php

namespace App\Service\Upload;

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class S3
 *
 * @package      App\Service\Upload
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski (https://www.nv3.eu)
 */
class S3
{
    /**
     * @var S3Client
     */
    private $client;

    /*
     * @var string
     */
    private $bucket;

    /**
     * S3 constructor.
     *
     * @param string $k
     * @param string $s
     * @param string $b
     * @param string $r
     */
    public function __construct(string $k, string $s, string $b, string $r) {
        $this->bucket = $b;
        $this->client = new S3Client(
            [
                'credentials' => [
                    'key' => $k,
                    'secret' => $s,
                ],
                'signature' => 'v4',
                'region' => $r,
                'version' => 'latest',
            ]
        );
    }

    /**
     * Upload single file to S3 bucket.
     *
     * @param UploadedFile $f
     * @param string       $n
     * @param string       $k
     *
     * @return \Aws\Result
     * @throws \Exception
     */
    public function uploadFileToS3(UploadedFile $f, string $n, string $k) {
        try {
            return $this->client->putObject(
                [
                    'Bucket' => $this->bucket,
                    'Key' => sprintf("%s/%s", $k, $n),
                    'SourceFile' => $f->getPath().'/'.$f->getFilename(),
                    'ContentType' => $f->getClientMimeType(),
                    'ACL' => 'public-read'
                ]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
