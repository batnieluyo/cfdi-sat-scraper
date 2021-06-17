<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiSatScraper\Captcha\Resolvers;

use PhpCfdi\CfdiSatScraper\Contracts\CaptchaResolverInterface;

class ConsoleCaptchaResolver implements CaptchaResolverInterface
{
    public function decode(string $base64Image): string
    {
        $temporaryFile = $this->storeImage($base64Image);
        $this->writeLine("\nResolve the captcha stored on file $temporaryFile: ");
        $decoded = $this->readLine();
        if (file_exists($temporaryFile)) {
            unlink($temporaryFile);
        }

        return $decoded;
    }

    public function storeImage(string $image): string
    {
        $filename = getcwd() . '/captcha.png';
        file_put_contents($filename, base64_decode($image));

        return $filename;
    }

    protected function writeLine(string $message): void
    {
        echo $message;
    }

    protected function readLine(): string
    {
        return trim(fgets(STDIN) ?: '');
    }
}
